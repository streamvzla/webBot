<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedEmail;
use App\Models\EmailAccount;
use App\Models\Platform;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ResellerQueryController extends Controller
{
    /**
     * Mostrar página de consulta de códigos para el revendedor.
     */
    public function index()
    {
        $user = auth()->user();

        // El revendedor (user) tiene un conjunto de AllowedEmails en su inventario.
        // Solo debe ver las plataformas creadas por su Administrador (Franquicia Raíz).
        $rootAdmin = $user->getRootFranchise();

        $platforms = Platform::where('is_active', true)
            ->where(function($q) use ($rootAdmin) {
                $q->where('user_id', $rootAdmin->id);
                if ($rootAdmin->id === 1) {
                    $q->orWhereNull('user_id'); // Por si hay plataformas huérfanas heredadas
                }
            })
            ->get();

        // Para el select de correos, cargamos solo los de su inventario
        $allowedEmails = AllowedEmail::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('email', 'asc')
            ->get();

        $setting = Setting::first();

        // Obtener datos del email temporal de la sesión si existen
        $emailBody = Session::get('reseller_email_body');
        $emailReceivedAt = Session::get('reseller_email_received_at');
        $tempCodeExpiry = Session::get('reseller_temp_code_expiry');
        $emailIsHtml = Session::get('reseller_email_is_html', false);

        return view('admin.query.index', compact('user', 'platforms', 'allowedEmails', 'setting', 'emailBody', 'emailReceivedAt', 'tempCodeExpiry', 'emailIsHtml'));
    }

    /**
     * Procesar consulta de código.
     */
    public function query(Request $request)
    {
        $user = auth()->user();
        $platformId = $request->input('platform_id');
        $email = $request->input('email');

        // Validar que se proporcione un email
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor selecciona un correo electrónico.',
            ], 400);
        }

        // Aislamiento estricto: El correo debe pertenecer a su inventario, sin excepciones
        $allowedEmail = AllowedEmail::where('email', $email)
            ->where('is_active', true)
            ->where('user_id', $user->id)
            ->first();

        if (!$allowedEmail) {
            return response()->json([
                'success' => false,
                'message' => 'El correo proporcionado no existe en tu inventario o está inactivo.',
            ], 403);
        }

        // Validar plataforma - debe existir y estar activa
        $platform = Platform::where('id', $platformId)->where('is_active', true)->first();

        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no válida o inactiva.',
            ], 400);
        }

        // Los revendedores NO tienen Rate Limit ni Cooldown. 
        // Pueden consultar directamente.

        // Buscar cuenta de correo asociada
        $emailAccount = null;

        if ($allowedEmail && $allowedEmail->email_account_id) {
            $emailAccount = EmailAccount::where('id', $allowedEmail->email_account_id)
                ->where('is_active', true)
                ->where('is_authorized', true)
                ->first();
        }

        if (!$emailAccount) {
            $emailAccount = EmailAccount::where('email', $email)
                ->where('is_active', true)
                ->where('is_authorized', true)
                ->first();
        }

        if (!$emailAccount) {
            // Usar una cuenta general
            $emailAccount = EmailAccount::where('user_id', 1)
                ->where('is_active', true)
                ->where('is_authorized', true)
                ->first();
        }

        if (!$emailAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ninguna cuenta de correo configurada o autorizada para revisar este servicio.',
            ], 400);
        }

        try {
            $subjects = $platform->subjects()->where('is_active', true)->pluck('subject')->toArray();

            if (empty($subjects)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay filtros configurados para esta plataforma.',
                ], 400);
            }

            // Mismo motor Centinela que usa el cliente
            $maxRetries = 6;
            $extractedCode = null;

            for ($i = 0; $i < $maxRetries; $i++) {
                $extractedCode = \App\Models\ExtractedCode::where('email_account_id', $emailAccount->id)
                    ->where('platform_id', $platform->id)
                    ->where('recipient_email', strtolower(trim($email)))
                    ->where('expires_at', '>', now())
                    ->latest()
                    ->first();

                if ($extractedCode) {
                    break;
                }
                
                // Si no hay código, pausar un poco y reintentar (simular polling)
                if ($i < $maxRetries - 1) {
                    sleep(2);
                }
            }

            // Registrar log de la consulta en Query pero marcando client_id a nulo (es revendedor)
            \App\Models\Query::create([
                'client_id'        => null,
                'user_id'          => $user->id,
                'email_account_id' => $emailAccount->id,
                'platform_id'      => $platform->id,
                'email'            => $email,
                'ip_address'       => $request->ip(),
                'user_agent'       => $request->userAgent(),
                'result'           => $extractedCode ? 'success' : 'no_code',
                'code_hash'        => $extractedCode ? \App\Models\Query::hashCode(substr($extractedCode->body ?? '', 0, 100)) : null,
                'code_status'      => $extractedCode ? 'found' : 'not_found',
            ]);

            if ($extractedCode) {
                // Guardar en sesión de forma aislada
                Session::put('reseller_email_body', $extractedCode->body);
                Session::put('reseller_email_received_at', $extractedCode->created_at->format('Y-m-d H:i:s'));
                Session::put('reseller_temp_code_expiry', now()->addMinutes(15)->timestamp);
                Session::put('reseller_email_is_html', true);
                
                Session::put('reseller_extracted_code', [
                    'type'  => $extractedCode->code_type ?? 'code',
                    'value' => $extractedCode->code,
                ]);

                return response()->json([
                    'success'     => true,
                    'code'        => $extractedCode->code,
                    'code_type'   => $extractedCode->code_type ?? 'code',
                    'message'     => 'Código extraído correctamente del Centinela.',
                    'url'         => route('admin.query.code'),
                    'received_at' => $extractedCode->created_at->format('Y-m-d H:i:s')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún código reciente para esta plataforma. Asegúrate de haberlo enviado.',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error en consulta de revendedor', [
                'user_id' => $user->id,
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al consultar el código.',
            ], 500);
        }
    }

    /**
     * Retorna el cuerpo del correo en un iframe
     */
    public function getTempCode()
    {
        $body = Session::get('reseller_email_body');
        $expiry = Session::get('reseller_temp_code_expiry');
        $isHtml = Session::get('reseller_email_is_html', false);

        if (!$body || !$expiry || now()->timestamp > $expiry) {
            Session::forget(['reseller_email_body', 'reseller_email_received_at', 'reseller_temp_code_expiry', 'reseller_email_is_html', 'reseller_extracted_code']);
            return response("<h1>El código ha expirado o no está disponible.</h1>", 404);
        }

        if (!$isHtml) {
            return response("<pre>" . htmlspecialchars($body) . "</pre>")->header('Content-Type', 'text/html; charset=UTF-8');
        }

        return response($body)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function clearSession()
    {
        Session::forget(['reseller_email_body', 'reseller_email_received_at', 'reseller_temp_code_expiry', 'reseller_email_is_html', 'reseller_extracted_code']);
        return redirect()->route('admin.query.index');
    }
}
