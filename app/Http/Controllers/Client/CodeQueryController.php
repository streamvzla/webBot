<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCodeRequest;
use App\Models\AllowedEmail;
use App\Models\Client;
use App\Models\EmailAccount;
use App\Models\Platform;
use App\Models\Query;
use App\Models\Setting;
use App\Models\User;
use App\Services\CodeExtractor;
use App\Services\ImapConnector;
use App\Services\EmailCodeExtractor;
use App\Services\QueryLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CodeQueryController extends Controller
{
    /**
     * Mostrar página de consulta de códigos.
     */
    public function index()
    {
        $client = Auth::guard('client')->user();

        // Obtener plataformas de la Franquicia Raíz del cliente + Globales del Super Admin
        $rootAdmin = $client->user ? $client->user->getRootFranchise() : null;
        $rootAdminId = $rootAdmin ? $rootAdmin->id : 1;
        
        $platforms = Platform::where('is_active', true)
            ->where(function($q) use ($rootAdminId) {
                $q->where('user_id', $rootAdminId)
                  ->orWhere('user_id', 1) // Plataformas globales del Super Admin
                  ->orWhereNull('user_id');
            })
            ->get();

        // NOTA: Si el cliente no tiene plataformas asignadas, verá un conjunto vacío.
        // Esto es intencional para que el administrador controle exactamente qué plataformas
        // puede ver cada cliente.

        $setting = Setting::first();

        // Obtener datos del email temporal de la sesión si existen
        $emailBody = Session::get('email_body');
        $emailReceivedAt = Session::get('email_received_at');
        $tempCodeExpiry = Session::get('temp_code_expiry');
        $emailIsHtml = Session::get('email_is_html', false);

        return view('client.query', compact('client', 'platforms', 'setting', 'emailBody', 'emailReceivedAt', 'tempCodeExpiry', 'emailIsHtml'));
    }

    /**
     * Procesar consulta de código.
     */
    public function query(Request $request)
    {
        $client = Auth::guard('client')->user();
        $platformId = $request->input('platform_id');
        $email = $request->input('email');

        // Verificar estado de suscripción de la Franquicia
        $rootAdmin = $client->user ? $client->user->getRootFranchise() : null;
        if ($rootAdmin && $rootAdmin->isSubscriptionExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'El servicio se encuentra temporalmente suspendido por falta de pago del administrador.',
            ], 403);
        }

        // Validar que se proporcione un email
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor ingresa tu correo electrónico.',
            ], 400);
        }

        // Verificar si el cliente tiene acceso a este correo
        if (!$client->hasAccessToEmail($email)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para consultar este correo. Contacta al administrador.',
            ], 403);
        }

        // Verificar si el email está autorizado (si el filtro está activo)
        $emailFilterEnabled = Setting::get(Setting::KEY_EMAIL_FILTER_ENABLED, false);
        $allowedEmail = null;

        if ($emailFilterEnabled) {
            $allowedEmail = AllowedEmail::where('email', $email)
                ->where('is_active', true)
                ->first();

            if (!$allowedEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu correo electrónico no está autorizado para realizar consultas. Contacta al administrador.',
                ], 403);
            }
        }

        // Validar plataforma - debe existir y estar activa
        $platform = Platform::where('id', $platformId)->where('is_active', true)->first();

        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no válida o inactiva.',
            ], 400);
        }

        // Verificar que la plataforma pertenece a la franquicia, al Super Admin (1), o es NULL
        if ($client->user && $platform->user_id !== null && $platform->user_id !== 1 && $platform->user_id !== $client->user->getRootFranchise()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta plataforma.',
            ], 403);
        }

        // Verificar rate limiting diario y estado
        $limiter = new QueryLimiter($client);
        
        if (!$client->is_active || $limiter->hasReachedDailyLimit()) {
            return response()->json([
                'success' => false,
                'message' => 'Límite diario alcanzado o cuenta inactiva.',
                'limit' => $limiter->getLimitStatus(),
            ], 429);
        }
        
        // Verificar cooldown específico por correo
        $cooldownMinutes = (int) \App\Models\Setting::get(\App\Models\Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);
        $lastQuery = \App\Models\Query::where('client_id', $client->id)
            ->where('email', $email)
            ->where('result', 'success') // Solo contar si realmente se encontró el código
            ->latest('created_at')
            ->first();
            
        if ($lastQuery) {
            $nextAllowedTime = $lastQuery->created_at->addMinutes($cooldownMinutes);
            if (now() < $nextAllowedTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes esperar para volver a consultar este correo.',
                    'limit' => [
                        'can_query' => false,
                        'seconds_until_next' => $nextAllowedTime->diffInSeconds(now()),
                    ],
                ], 429);
            }
        }

        // Buscar cuenta de correo:
        // 1. Si hay un AllowedEmail con email_account_id, usar esa cuenta
        // 2. Sino, buscar por el email proporcionado
        // 3. Si aún no hay cuenta, usar la primera cuenta activa como predeterminada
        $emailAccount = null;

        if ($allowedEmail && $allowedEmail->email_account_id) {
            // Usar la cuenta IMAP asociada al email autorizado
            $emailAccount = EmailAccount::where('id', $allowedEmail->email_account_id)
                ->where('is_active', true)
                ->where('is_authorized', true)
                ->first();
        }

        // Si no encontramos cuenta por AllowedEmail, buscar por el email del cliente
        if (!$emailAccount) {
            $emailAccount = EmailAccount::where('email', $email)
                ->where('is_active', true)
                ->where('is_authorized', true)
                ->first();
        }

        // Si aún no hay cuenta, usar la primera cuenta activa del usuario padre
        if (!$emailAccount && $client->user) {
            // Buscar en las cuentas del usuario padre (relación one-to-many por user_id)
            $emailAccount = EmailAccount::where('user_id', $client->user->id)
                ->where('is_active', true)
                ->where('is_authorized', true)
                ->first();

            // Si no hay cuenta por user_id, buscar en la relación many-to-many
            if (!$emailAccount) {
                $emailAccount = $client->user->emailAccounts()
                    ->where('is_active', true)
                    ->where('is_authorized', true)
                    ->first();
            }
        }


        Log::info('Cuenta de correo seleccionada para consulta', [
            'client_id' => $client->id,
            'email_account_id' => $emailAccount?->id,
            'email_account_email' => $emailAccount?->email,
            'has_user' => $client->user ? 'yes' : 'no',
        ]);

        if (!$emailAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ninguna cuenta de correo configurada en la configuración IMAP para revisar el correo.',
            ], 400);
        }

        // Verificar que el servidor está autorizado para consultas
        if (!$emailAccount->is_authorized) {
            Log::warning('Intento de consulta en servidor no autorizado', [
                'client_id' => $client->id,
                'email_account_id' => $emailAccount->id,
                'email_account_email' => $emailAccount->email,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'El servidor de correo no está autorizado para realizar consultas. Contacta al administrador.',
            ], 403);
        }

        try {
            // Obtener subjects de la plataforma para validación
            $subjects = $platform->subjects()->where('is_active', true)->pluck('subject')->toArray();

            if (empty($subjects)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay filtros configurados para esta plataforma.',
                ], 400);
            }

            Log::info('Buscando códigos extraídos en Base de Datos (Centinela)', [
                'platform' => $platform->name,
                'email_account' => $emailAccount->email,
                'recipient_email' => $email,
            ]);

            // ─────────────────────────────────────────────────────────────────
            // NUEVA ARQUITECTURA: EXTRACCIÓN BAJO DEMANDA (EN VIVO)
            // Ya no usamos el Centinela ni workers en background. 
            // Nos conectamos a IMAP solo cuando el cliente hace esta solicitud.
            // ─────────────────────────────────────────────────────────────────
            $extractedCode = null;
            $cacheKey = 'live_imap_query_' . md5($email . '_' . $emailAccount->id);
            
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $cachedResult = \Illuminate\Support\Facades\Cache::get($cacheKey);
                if ($cachedResult !== 'not_found') {
                    $extractedCode = (object) $cachedResult; // Mocked for the rest of the controller
                }
            } else {
                try {
                    $connector = new \App\Services\ImapConnector($emailAccount);
                    $connector->connect();

                    $messages = $connector->getRecentEmails(1);
                    $expectedRecipients = [strtolower(trim($email))];
                    $platformSubjects = [$platform->name => $platform->subject_keywords];

                    foreach ($messages as $message) {
                        $emailData = $connector->searchByTo($message, $expectedRecipients, $platformSubjects);
                        
                        if ($emailData) {
                            $cleanText = strip_tags($emailData['body']);
                            $extracted = \App\Services\EmailCodeExtractor::extract($emailData['body'], $cleanText);
                            $val = is_array($extracted) ? ($extracted['value'] ?? null) : $extracted;
                            
                            if ($val) {
                                $extractedCode = (object) [
                                    'body' => $emailData['body'],
                                    'code' => $val,
                                    'type' => is_array($extracted) ? ($extracted['type'] ?? 'code') : 'code',
                                    'created_at' => now(), // Mocked Carbon instance
                                ];
                                break;
                            }
                        }
                    }
                    
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $extractedCode ? (array) $extractedCode : 'not_found', 10);
                    
                    try {
                        $connector->disconnect();
                    } catch (\Throwable $e) {}
                    
                } catch (\Throwable $e) {
                    Log::error("Error en CodeQueryController (On-Demand): " . $e->getMessage());
                }
            }

            // Registrar consulta en la tabla queries
            Query::create([
                'client_id'        => $client->id,
                'user_id'          => $client->user ? $client->user->id : null,
                'email_account_id' => $emailAccount->id,
                'platform_id'      => $platform->id,
                'email'            => $email,
                'ip_address'       => $request->ip(),
                'user_agent'       => $request->userAgent(),
                'result'           => $extractedCode ? 'success' : 'no_code',
                'code_hash'        => $extractedCode ? Query::hashCode(substr($extractedCode->body ?? '', 0, 100)) : null,
                'code_status'      => $extractedCode ? 'found' : 'not_found',
            ]);

            // Registrar consulta (rate limiting) si se encontró o si es el último intento
            if ($extractedCode || $request->has('is_final_attempt')) {
                $limiter->recordQuery();
            }

            if ($extractedCode) {
                $displaySeconds = config('app.code_display_seconds', 60);

                Session::put('email_body',        $extractedCode->body);
                $createdAt = is_string($extractedCode->created_at) ? \Carbon\Carbon::parse($extractedCode->created_at) : clone $extractedCode->created_at;
                
                Session::put('email_received_at', $createdAt->format('Y-m-d H:i:s'));
                Session::put('temp_code_expiry',  now()->addSeconds($displaySeconds)->timestamp);
                Session::put('email_is_html',     true);
                Session::put('extracted_code', [
                    'type'  => $extractedCode->code_type ?? 'code',
                    'value' => $extractedCode->code,
                ]);

                return response()->json([
                    'success'    => true,
                    'message'    => 'Email encontrado.',
                    'code'       => $extractedCode->code,
                    'code_type'  => $extractedCode->code_type ?? 'code',
                    'subject'    => $extractedCode->subject ?? '',
                    'email_body' => $extractedCode->body,
                    'received_at'=> $extractedCode->created_at->format('Y-m-d H:i:s'),
                    'expires_in' => $displaySeconds,
                    'is_html'    => true,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró un código reciente válido en el correo proporcionado. Por favor, asegúrate de haber enviado el código y vuelve a intentarlo en unos segundos.',
            ], 404);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorType = class_basename($e);

            $userMessage = 'Error al procesar la consulta. Intenta de nuevo.';

            Log::error('Error en consulta de código', [
                'client_id' => $client->id,
                'platform' => $platform->name,
                'email' => $email,
                'email_account' => $emailAccount->email ?? 'none',
                'error_type' => $errorType,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString(),
            ]);

            // Registrar consulta fallida
            Query::create([
                'client_id' => $client->id,
                'user_id' => $client->user ? $client->user->id : null,
                'email_account_id' => $emailAccount->id ?? 0,
                'platform_id' => $platform->id,
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'result' => 'error',
                'code_status' => 'error',
            ]);

            // En desarrollo, devolver más detalles del error
            $debug = config('app.debug');

            return response()->json([
                'success' => false,
                'message' => $userMessage,
                'error_type' => $errorType,
                'debug' => $debug ? $errorMessage : null,
            ], 500);
        }
    }

    /**
     * Obtener código temporal de la sesión.
     */
    public function getTempCode(Request $request)
    {
        $emailBody = Session::get('email_body');
        $emailReceivedAt = Session::get('email_received_at');
        $tempCodeExpiry = Session::get('temp_code_expiry');

        if (!$emailBody || !$tempCodeExpiry) {
            return response()->json([
                'success' => false,
                'message' => 'No hay email disponible.',
            ], 404);
        }

        if (now()->timestamp > $tempCodeExpiry) {
            Session::forget(['email_body', 'email_received_at', 'temp_code_expiry']);

            return response()->json([
                'success' => false,
                'message' => 'El email ha expirado.',
                'expired' => true,
            ], 410);
        }

        return response()->json([
            'success' => true,
            'email_body' => $emailBody,
            'received_at' => $emailReceivedAt,
            'expires_in' => $tempCodeExpiry - now()->timestamp,
        ]);
    }



    /**
     * Verificar estado de la plataforma.
     */
    public function checkPlatform(Request $request, int $platformId)
    {
        $client = Auth::guard('client')->user();
        $platform = Platform::find($platformId);

        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no encontrada.',
            ], 404);
        }

        if (!$platform->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'La plataforma está inactiva.',
                'platform' => [
                    'id' => $platform->id,
                    'name' => $platform->name,
                    'is_active' => false,
                ],
            ], 503);
        }

        // Verificar que la plataforma pertenece al usuario padre del cliente
        if ($client->user && $platform->user_id !== $client->user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta plataforma.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'platform' => [
                'id' => $platform->id,
                'name' => $platform->name,
                'icon' => $platform->icon,
                'is_active' => true,
            ],
        ]);
    }

    /**
     * Diagnosticar conexión IMAP de una cuenta.
     */
    public function diagnoseImap(Request $request)
    {
        // Buscar cuenta de correo activa del usuario padre del cliente autenticado
        $client = auth('client')->user();
        
        // Evitar que esta petición lenta bloquee la sesión para otras peticiones concurrentes (ej. cargar correos)
        session()->save();
        $user   = $client?->user;

        $emailAccount = null;
        if ($user) {
            $emailAccount = EmailAccount::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('is_authorized', true)
                ->first();
            if (!$emailAccount) {
                $emailAccount = $user->emailAccounts()
                    ->where('is_active', true)
                    ->where('is_authorized', true)
                    ->first();
            }
        }


        if (!$emailAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ninguna cuenta de correo configurada en la configuración IMAP para revisar el correo.',
                'diagnostics' => [
                    'account_found' => false,
                ],
            ], 400);
        }

        $diagnostics = [
            'account_found' => true,
            'email' => $emailAccount->email,
            'host' => $emailAccount->imap_host,
            'port' => $emailAccount->imap_port,
            'encryption' => $emailAccount->imap_encryption,
            'username' => $emailAccount->username,
            'password_configured' => !empty($emailAccount->imap_password),
        ];

        try {
            // Probar conexión al puerto
            $portTest = ImapConnector::testConnection(
                $emailAccount->imap_host,
                $emailAccount->imap_port,
                $emailAccount->imap_encryption
            );
            $diagnostics['port_accessible'] = $portTest['success'];
            $diagnostics['port_test_message'] = $portTest['message'];

            // Intentar conectar
            $connector = new ImapConnector($emailAccount);
            $connector->connect();

            // Obtener estado completo
            $connectionStatus = $connector->getConnectionStatus();
            $diagnostics['connection'] = $connectionStatus;

            // Obtener bandejas disponibles
            $mailboxes = $connector->getMailboxes();
            $diagnostics['mailboxes'] = $mailboxes;

            $connector->disconnect();

            return response()->json([
                'success' => true,
                'message' => 'Diagnóstico completado exitosamente',
                'diagnostics' => $diagnostics,
            ]);

        } catch (\Exception $e) {
            Log::error('Error en diagnóstico IMAP', [
                'email_account' => $emailAccount->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al diagnosticar conexión IMAP',
                'error' => $e->getMessage(),
                'diagnostics' => $diagnostics,
            ], 500);
        }
    }

    /**
     * Listar emails recientes (para monitoreo).
     */
    public function listRecentEmails(Request $request)
    {
        $email = $request->input('email');
        $hours = $request->input('hours', 1);

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor ingresa tu correo electrónico.',
            ], 400);
        }

        $emailAccount = EmailAccount::where('email', $email)
            ->where('is_active', true)
            ->first();

        if (!$emailAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró una cuenta de correo activa.',
            ], 400);
        }

        // Verificar que la cuenta pertenece al usuario padre del cliente
        $client = auth('client')->user();
        if ($client && $client->user) {
            $userAccountIds = EmailAccount::where('user_id', $client->user->id)->pluck('id');
            $relatedIds = $client->user->emailAccounts()->pluck('email_accounts.id');
            $allowedIds = $userAccountIds->merge($relatedIds)->unique();
            if (!$allowedIds->contains($emailAccount->id)) {
                return response()->json(['success' => false, 'message' => 'No tienes acceso a esta cuenta.'], 403);
            }
        }

        try {
            $connector = new ImapConnector($emailAccount);
            $connector->connect();

            $recentEmails = $connector->getRecentEmails($hours);
            $emailList = [];

            foreach ($recentEmails as $uid) {
                $overview = @imap_fetch_overview($connector->getConnection(), $uid, FT_UID);
                if ($overview) {
                    $emailList[] = [
                        'uid' => $uid,
                        'subject' => imap_utf8($overview[0]->subject),
                        'from' => imap_utf8($overview[0]->from),
                        'date' => $overview[0]->date,
                        'seen' => (bool) $overview[0]->seen,
                    ];
                }
            }

            $connector->disconnect();

            return response()->json([
                'success' => true,
                'message' => "Se encontraron " . count($emailList) . " emails en las últimas {$hours} horas",
                'emails' => $emailList,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar emails recientes', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al listar emails',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar qué subjects coinciden con los emails recientes.
     */
    public function matchSubjects(Request $request)
    {
        $email = $request->input('email');
        $platformId = $request->input('platform_id');
        $hours = $request->input('hours', 24);

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor ingresa tu correo electrónico.',
            ], 400);
        }

        if (!$platformId) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor selecciona una plataforma.',
            ], 400);
        }

        $emailAccount = EmailAccount::where('email', $email)
            ->where('is_active', true)
            ->first();

        if (!$emailAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró una cuenta de correo activa.',
            ], 400);
        }

        // Verificar que la cuenta pertenece al usuario padre del cliente
        $client = auth('client')->user();
        if ($client && $client->user) {
            $userAccountIds = EmailAccount::where('user_id', $client->user->id)->pluck('id');
            $relatedIds = $client->user->emailAccounts()->pluck('email_accounts.id');
            $allowedIds = $userAccountIds->merge($relatedIds)->unique();
            if (!$allowedIds->contains($emailAccount->id)) {
                return response()->json(['success' => false, 'message' => 'No tienes acceso a esta cuenta.'], 403);
            }
        }

        $platform = Platform::find($platformId);
        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no encontrada.',
            ], 404);
        }

        $subjects = $platform->subjects()->where('is_active', true)->pluck('subject')->toArray();

        try {
            $connector = new ImapConnector($emailAccount);
            $connector->connect();

            $recentEmails = $connector->getRecentEmails($hours);
            $matchedEmails = [];
            $unmatchedEmails = [];

            foreach ($recentEmails as $uid) {
                $overview = @imap_fetch_overview($connector->getConnection(), $uid, FT_UID);
                if ($overview) {
                    $emailSubject = imap_utf8($overview[0]->subject);
                    $matches = false;

                    foreach ($subjects as $subject) {
                        if (stripos($emailSubject, $subject) !== false) {
                            $matches = true;
                            break;
                        }
                    }

                    $emailData = [
                        'uid' => $uid,
                        'subject' => $emailSubject,
                        'from' => imap_utf8($overview[0]->from),
                        'date' => $overview[0]->date,
                    ];

                    if ($matches) {
                        $matchedEmails[] = $emailData;
                    } else {
                        $unmatchedEmails[] = $emailData;
                    }
                }
            }

            $connector->disconnect();

            return response()->json([
                'success' => true,
                'message' => 'Análisis de subjects completado',
                'data' => [
                    'platform' => $platform->name,
                    'subjects_configured' => $subjects,
                    'total_emails' => count($recentEmails),
                    'matched_emails' => count($matchedEmails),
                    'unmatched_emails' => count($unmatchedEmails),
                    'matched' => $matchedEmails,
                    'unmatched' => array_slice($unmatchedEmails, 0, 10), // Limit to 10
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar subjects', [
                'email' => $email,
                'platform' => $platform->name,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar subjects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener lista de emails autorizados para una plataforma.
     */
    public function getEmailsByPlatform(Request $request)
    {
        $client = Auth::guard('client')->user();
        $platformId = $request->input('platform_id');
        $search = $request->input('search', '');

        if (!$platformId) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor selecciona una plataforma.',
            ], 400);
        }

        // Validar que la plataforma existe y está activa
        $platform = Platform::where('id', $platformId)->where('is_active', true)->first();

        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no válida o inactiva.',
            ], 400);
        }

        // Verificar que la plataforma pertenece al usuario padre del cliente
        // Si user_id es NULL, es una plataforma global accesible para todos
        if ($client->user && $platform->user_id !== null && $platform->user_id !== $client->user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta plataforma.',
            ], 403);
        }

        // Recopilar emails según el modo de acceso del cliente
        $emailIds = [];

        if ($client->access_mode === 'all') {
            // MODO ACCESO TOTAL: El cliente puede ver TODOS los correos del usuario padre
            // SOLO los correos del usuario padre, no los globales del sistema
            if ($client->user) {
                // Emails del usuario padre (user_id coincide)
                $userEmailIds = $client->user->allowedEmails()
                    ->where('is_active', true)
                    ->pluck('allowed_emails.id')
                    ->toArray();
                $emailIds = array_merge($emailIds, $userEmailIds);
            }
            // Si no tiene usuario padre, no tendrá acceso a ningún email
            // (no mostraremos emails globales para mantener el aislamiento)
        } else {
            // MODO ACCESO SELECTIVO: El cliente solo puede ver los correos
            // que le han sido asignados específicamente
            $clientEmailIds = $client->allowedEmails()
                ->where('is_active', true)
                ->pluck('allowed_emails.id')
                ->toArray();
            $emailIds = array_merge($emailIds, $clientEmailIds);
        }

        // Eliminar duplicados
        $emailIds = array_unique($emailIds);

        // Si no hay emails, retornar vacío
        if (empty($emailIds)) {
            return response()->json([
                'success' => true,
                'emails' => [],
                'platform' => [
                    'id' => $platform->id,
                    'name' => $platform->name,
                ],
            ]);
        }

        // Construir consulta
        $query = AllowedEmail::whereIn('id', $emailIds);

        // Filtrar estrictamente por plataforma específica (no mostrar correos sin plataforma)
        $query->where('platform_id', $platformId);

        // Obtener los correos que tienen una garantía de reemplazo activa para este cliente
        $excludedEmails = \App\Models\WarrantyRequest::where('client_id', $client->id)
            ->where('type', 'replacement')
            ->whereIn('status', ['pending', 'approved', 'resolved'])
            ->pluck('old_email')
            ->toArray();

        // Solo emails activos y NO pausados
        $query->where('is_active', true)
              ->whereNull('paused_at');

        if (!empty($excludedEmails)) {
            $query->whereNotIn('email', $excludedEmails);
        }

        // Aplicar búsqueda si se proporciona
        if ($search) {
            $query->where('email', 'like', "%{$search}%");
        }

        $emails = $query->orderBy('email')->get();

        $cooldownMinutes = (int) \App\Models\Setting::get(\App\Models\Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);
        
        // Transformar para el dropdown y calcular cooldown
        $emailList = $emails->map(function ($emailModel) use ($client, $cooldownMinutes) {
            $cooldownSeconds = 0;
            
            $lastQuery = \App\Models\Query::where('client_id', $client->id)
                ->where('email', $emailModel->email)
                ->where('result', 'success') // Solo aplicar cooldown si hubo éxito en consultas pasadas
                ->latest('created_at')
                ->first();
                
            if ($lastQuery) {
                $nextAllowedTime = $lastQuery->created_at->addMinutes($cooldownMinutes);
                if (now() < $nextAllowedTime) {
                    $cooldownSeconds = $nextAllowedTime->diffInSeconds(now());
                }
            }
            
            return [
                'id' => $emailModel->id,
                'email' => $emailModel->email,
                'description' => $emailModel->description,
                'cooldown' => $cooldownSeconds,
            ];
        });

        return response()->json([
            'success' => true,
            'emails' => $emailList,
            'platform' => [
                'id' => $platform->id,
                'name' => $platform->name,
            ],
        ]);
    }
    /**
     * Limpia la sesión de consulta actual y redirige al inicio.
     */
    public function clearSession()
    {
        Session::forget(['email_body', 'extracted_code', 'email_received_at', 'temp_code_expiry', 'email_is_html']);
        return redirect()->route('client.query');
    }
}

