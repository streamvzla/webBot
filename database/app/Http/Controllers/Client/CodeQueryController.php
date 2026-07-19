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

        // Obtener plataformas habilitadas para el cliente (relación many-to-many)
        $platforms = $client->platforms()->where('is_active', true)->get();

        // Si no hay plataformas asignadas directamente, buscar las del usuario padre
        if ($platforms->isEmpty() && $client->user) {
            $platforms = $client->user->platforms()->where('is_active', true)->get();
        }

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

        // Verificar que la plataforma pertenece al usuario padre del cliente
        if ($client->user && $platform->user_id !== $client->user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta plataforma.',
            ], 403);
        }

        // Verificar rate limiting
        $limiter = new QueryLimiter($client);

        if (!$limiter->canMakeQuery()) {
            $status = $limiter->getLimitStatus();

            return response()->json([
                'success' => false,
                'message' => 'Límite de consultas alcanzado.',
                'limit' => $status,
            ], 429);
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

        // Si aún no hay cuenta, usar la primera cuenta activa como predeterminada global
        if (!$emailAccount) {
            $emailAccount = EmailAccount::where('is_active', true)
                ->where('is_authorized', true)
                ->first();
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
                'message' => 'No hay ninguna cuenta de correo configurada para realizar consultas.',
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
            // Conectar a IMAP (el ImapConnector maneja la desencriptación de contraseña)
            $connector = new ImapConnector($emailAccount);
            $connector->connect();

            // Obtener subjects de la plataforma
            $subjects = $platform->subjects()->where('is_active', true)->pluck('subject')->toArray();

            if (empty($subjects)) {
                $connector->disconnect();

                return response()->json([
                    'success' => false,
                    'message' => 'No hay filtros configurados para esta plataforma.',
                ], 400);
            }

            Log::info('Buscando códigos en emails', [
                'platform' => $platform->name,
                'email_account' => $emailAccount->email,
                'recipient_email' => $email,
                'subjects' => $subjects,
            ]);

            // Buscar email por destinatario Y plataforma (subject)
            $emailData = $connector->findLatestEmailByRecipientAndPlatform($email, $subjects, 72);

            $foundCode = $emailData !== null;
            $emailsSearched = $foundCode ? 1 : 0;
            $result = $foundCode ? 'success' : 'no_code';

            $connector->disconnect();

            Log::info('Consulta IMAP completada', [
                'platform' => $platform->name,
                'recipient_email' => $email,
                'emails_searched' => $emailsSearched,
                'code_found' => $foundCode ? 'yes' : 'no',
                'subjects_searched' => $subjects,
            ]);

            // Registrar consulta en la tabla queries
            $queryRecord = Query::create([
                'client_id' => $client->id,
                'user_id' => $client->user ? $client->user->id : null,
                'email_account_id' => $emailAccount->id,
                'platform_id' => $platform->id,
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'result' => $result,
                'code_hash' => $emailData ? Query::hashCode(substr($emailData['body'], 0, 100)) : null,
                'code_status' => $emailData ? 'found' : 'not_found',
            ]);

            // Registrar consulta (rate limiting)
            $limiter->recordQuery();

            if ($emailData) {
                // Guardar datos del email temporal en sesión (60 segundos por defecto)
                $displaySeconds = config('app.code_display_seconds', 60);
                Session::put('email_body', html_entity_decode($emailData['body']));
                Session::put('email_received_at', $emailData['received_at']);
                Session::put('temp_code_expiry', now()->addSeconds($displaySeconds)->timestamp);
                Session::put('email_is_html', $emailData['is_html'] ?? false);

                return response()->json([
                    'success' => true,
                    'message' => 'Email encontrado.',
                    'email_body' => html_entity_decode($emailData['body']),
                    'received_at' => $emailData['received_at'],
                    'expires_in' => $displaySeconds,
                    'is_html' => $emailData['is_html'] ?? false,
                ]);
            }

            // No se encontró email - dar información más detallada
            $debugMessage = config('app.debug') ?
                "No se encontró email con los filtros: destinatario={$email}, plataforma={$platform->name}, subjects=" . implode(', ', $subjects) :
                null;

            return response()->json([
                'success' => false,
                'message' => 'No se encontró email reciente para esta plataforma. Verifica que el email sea correcto y que hayas solicitado el código recientemente.',
                'result' => 'no_code',
                'debug' => $debugMessage,
            ]);

        } catch (\Exception $e) {
            // Cerrar conexión si está abierta
            if (isset($connector) && $connector->isConnected()) {
                try {
                    $connector->disconnect();
                } catch (\Exception $disconnectEx) {
                    Log::warning('Error al cerrar conexión IMAP', ['error' => $disconnectEx->getMessage()]);
                }
            }

            $errorMessage = $e->getMessage();
            $errorType = class_basename($e);

            // Determinar el tipo de error y mensaje apropiado
            $userMessage = 'Error al procesar la consulta. Intenta de nuevo.';

            // Errores específicos con mensajes más informativos
            if (strpos($errorMessage, 'IMAP') !== false || strpos($errorMessage, 'imap') !== false) {
                if (strpos($errorMessage, 'timeout') !== false || strpos($errorMessage, 'timed out') !== false) {
                    $userMessage = 'La conexión al servidor de correo tardó demasiado. Intenta de nuevo en unos segundos.';
                } elseif (strpos($errorMessage, 'Puerto') !== false) {
                    $userMessage = 'No se puede acceder al servidor de correo en este momento. Contacta al administrador.';
                } else {
                    $userMessage = 'Error de conexión con el servidor de correo. Intenta de nuevo más tarde.';
                }
            } elseif (strpos($errorMessage, 'no hay conexión') !== false) {
                $userMessage = 'Se perdió la conexión con el servidor. Intenta de nuevo.';
            } elseif (strpos($errorMessage, ' AUTHENTICATION') !== false || strpos($errorMessage, 'login') !== false) {
                $userMessage = 'Error de autenticación con el servidor de correo. Contacta al administrador.';
            }

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
     * Obtener estado del límite.
     */
    public function getLimitStatus()
    {
        $client = Auth::guard('client')->user();
        $limiter = new QueryLimiter($client);

        return response()->json([
            'limit_status' => $limiter->getLimitStatus(),
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
        // Buscar la primera cuenta de correo activa configurada en el sistema
        $emailAccount = EmailAccount::where('is_active', true)->first();

        if (!$emailAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ninguna cuenta de correo configurada en el sistema.',
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
        if ($client->user && $platform->user_id !== $client->user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta plataforma.',
            ], 403);
        }

        // Recopilar emails de diferentes fuentes
        $emailIds = [];

        // 1. Emails del cliente asignados directamente (si no tiene acceso total)
        if ($client->access_mode !== 'all') {
            $clientEmailIds = $client->allowedEmails()->pluck('allowed_emails.id')->toArray();
            $emailIds = array_merge($emailIds, $clientEmailIds);
        }

        // 2. Emails del usuario padre
        if ($client->user) {
            $userEmailIds = $client->user->allowedEmails()->pluck('allowed_emails.id')->toArray();
            $emailIds = array_merge($emailIds, $userEmailIds);
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

        // Filtrar por plataforma específica O emails sin platform_id (genéricos)
        $query->where(function ($q) use ($platformId) {
            $q->where('platform_id', $platformId)
              ->orWhereNull('platform_id');
        });

        // Solo emails activos
        $query->where('is_active', true);

        // Aplicar búsqueda si se proporciona
        if ($search) {
            $query->where('email', 'like', "%{$search}%");
        }

        $emails = $query->orderBy('email')->get();

        // Transformar para el dropdown
        $emailList = $emails->map(function ($email) {
            return [
                'id' => $email->id,
                'email' => $email->email,
                'description' => $email->description,
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
}
