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

        // Verificar si el email tiene permitido consultas públicas
        $allowedEmail = AllowedEmail::where('email', $email)
            ->where('is_active', true)
            ->where('is_public', true)
            ->first();

        if (!$allowedEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Tu correo electrónico no está autorizado para consultas públicas.',
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

        // Para usuarios públicos, usar una cuenta por defecto o buscar por email
        $emailAccount = null;

        // Buscar por el email proporcionado
        if (!$emailAccount) {
            $emailAccount = EmailAccount::where('email', $email)
                ->where('is_active', true)
                ->first();
        }

        // Si aún no hay cuenta, usar la primera cuenta activa como predeterminada
        if (!$emailAccount) {
            $emailAccount = EmailAccount::where('is_active', true)->first();
        }

        if (!$emailAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ninguna cuenta de correo configurada para realizar consultas.',
            ], 400);
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

            Log::info('Consulta pública IMAP', [
                'platform' => $platform->name,
                'email_account' => $emailAccount->email,
                'recipient_email' => $email,
                'subjects' => $subjects,
            ]);

            // Buscar email por destinatario Y plataforma (subject)
            $emailData = $connector->findLatestEmailByRecipientAndPlatform($email, $subjects);

            $foundCode = $emailData !== null;
            $emailsSearched = $foundCode ? 1 : 0;
            $result = $foundCode ? 'success' : 'no_code';

            $connector->disconnect();

            // Registrar consulta pública en la tabla queries
            Query::create([
                'email_account_id' => $emailAccount->id,
                'platform_id' => $platform->id,
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'result' => $result,
                'code_hash' => $emailData ? Query::hashCode(substr($emailData['body'], 0, 100)) : null,
                'code_status' => $emailData ? 'found' : 'not_found',
            ]);

            Log::info('Consulta pública IMAP completada', [
                'platform' => $platform->name,
                'recipient_email' => $email,
                'emails_searched' => $emailsSearched,
                'code_found' => $foundCode ? 'yes' : 'no',
            ]);

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

            return response()->json([
                'success' => false,
                'message' => 'No se encontró email reciente para esta plataforma.',
                'result' => 'no_code',
            ]);

        } catch (\Exception $e) {
            Log::error('Error en consulta pública de código', [
                'platform' => $platform->name,
                'email' => $email,
                'email_account' => $emailAccount->email ?? 'none',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Registrar consulta pública fallida
            Query::create([
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
}
