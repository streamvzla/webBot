<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AllowedEmail;
use App\Models\EmailAccount;
use App\Models\Platform;
use App\Models\Query;
use App\Models\Setting;
use App\Services\ImapConnector;
use App\Services\QueryLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PublicQueryController extends Controller
{
    /**
     * Mostrar página pública de consulta de códigos.
     */
    public function index()
    {
        $platforms = Platform::where('is_active', true)->get();
        $setting = Setting::first();

        // Obtener datos del email temporal de la sesión si existen
        $emailBody = Session::get('email_body');
        $emailReceivedAt = Session::get('email_received_at');
        $tempCodeExpiry = Session::get('temp_code_expiry');
        $emailIsHtml = Session::get('email_is_html', false);

        return view('public.query', compact('platforms', 'setting', 'emailBody', 'emailReceivedAt', 'tempCodeExpiry', 'emailIsHtml'));
    }

    /**
     * Procesar consulta pública de código (sin autenticación).
     */
    public function query(Request $request)
    {
        $platformId = $request->input('platform_id');
        $email = $request->input('email');

        // Validar que se proporcione un email
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor ingresa tu correo electrónico.',
            ], 400);
        }

        // Verificar si el email tiene permitido consultas públicas y está asignado a un cliente activo
        $allowedEmail = AllowedEmail::where('email', $email)
            ->where('is_active', true)
            ->where('is_public', true)
            ->occupied()
            ->first();

        if (!$allowedEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Tu correo electrónico no está autorizado o no cuenta con una asignación activa para consultas públicas.',
            ], 403);
        }

        // Verificar estado de suscripción de la Franquicia dueña del correo
        $rootAdmin = $allowedEmail->user ? $allowedEmail->user->getRootFranchise() : null;
        if ($rootAdmin && $rootAdmin->isSubscriptionExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'El servicio se encuentra temporalmente suspendido por falta de pago del administrador.',
            ], 403);
        }

        // Validar plataforma
        $platform = Platform::where('id', $platformId)->where('is_active', true)->first();

        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no válida o inactiva.',
            ], 400);
        }

        try {
            // BUSQUEDA INSTANTANEA EN LA BASE DE DATOS DEL CENTINELA
            $extractedCodeModel = \App\Models\ExtractedCode::where('recipient_email', $email)
                ->where('platform_id', $platform->id)
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->first();

            $foundCode = $extractedCodeModel !== null;

            // Validación de Subject Público (Opción B: Filtrado Posterior)
            if ($foundCode) {
                $isPublicSubject = false;
                $publicSubjects = $platform->subjects()->where('is_public', true)->get();
                
                foreach ($publicSubjects as $pubSub) {
                    $pattern = str_replace('[email]', '*', $pubSub->subject);
                    if (\Illuminate\Support\Str::is($pattern, $extractedCodeModel->subject) || \Illuminate\Support\Str::is('*' . $pattern . '*', $extractedCodeModel->subject)) {
                        $isPublicSubject = true;
                        break;
                    }
                }

                if (!$isPublicSubject) {
                    $foundCode = false; // Ocultar el código si no proviene de un subject público
                }
            }

            $result = $foundCode ? 'success' : 'no_code';

            // Registrar consulta pública en la tabla queries
            Query::create([
                'email_account_id' => null,
                'platform_id' => $platform->id,
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'result' => $result,
                'code_hash' => $foundCode ? Query::hashCode($extractedCodeModel->code) : null,
                'code_status' => $foundCode ? 'found' : 'not_found',
            ]);

            Log::info('Consulta publica BD completada', [
                'platform' => $platform->name,
                'recipient_email' => $email,
                'code_found' => $foundCode ? 'yes' : 'no',
            ]);

            if ($foundCode) {
                $displaySeconds = config('app.code_display_seconds', 60);
                
                Session::put('email_body', $extractedCodeModel->body);
                Session::put('extracted_code', $extractedCodeModel->code);
                Session::put('email_received_at', $extractedCodeModel->created_at->format('Y-m-d H:i:s'));
                Session::put('temp_code_expiry', now()->addSeconds($displaySeconds)->timestamp);
                Session::put('email_is_html', true);

                return response()->json([
                    'success' => true,
                    'message' => 'Código encontrado al instante.',
                    'email_body' => $extractedCodeModel->body,
                    'received_at' => $extractedCodeModel->created_at->format('Y-m-d H:i:s'),
                    'expires_in' => $displaySeconds,
                    'is_html' => true,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún código reciente para esta plataforma.',
                'result' => 'no_code',
            ]);

        } catch (\Exception $e) {
            Log::error('Error en consulta pública de código', [
                'platform' => $platform->name,
                'email' => $email,
                'email_account' => 'public_query',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Registrar consulta pública fallida
            Query::create([
                'email_account_id' => null,
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
                'message' => 'Error al procesar la consulta. Intenta de nuevo.',
                'error_type' => class_basename($e),
                'debug' => $debug ? $e->getMessage() : null,
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
     * Limpia la sesión de consulta actual y redirige al inicio.
     */
    public function clearSession()
    {
        Session::forget(['email_body', 'extracted_code', 'email_received_at', 'temp_code_expiry', 'email_is_html']);
        return redirect()->route('public.query');
    }
}
