<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AutoLicenseController extends Controller
{
    /**
     * Crea una nueva licencia Y usuario admin/franquiciado automáticamente
     * desde StreamVzla / tiendaOnline al recibir un pago.
     */
    public function create(Request $request)
    {
        // ── 1. VERIFICAR SECRETO DE SEGURIDAD ──────────────────────────────────
        $secret = $request->header('X-BotCodigo-Secret') ?: $request->input('secret');
        $expectedSecret = config('services.botcodigo.webhook_secret', env('BOTCODIGO_WEBHOOK_SECRET', 'streamvzla_auto_license_secret_2026'));

        if (!$secret || $secret !== $expectedSecret) {
            Log::warning("Intento fallido de creación automática de licencia desde IP: " . $request->ip());
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Token de seguridad inválido.'
            ], 401);
        }

        // ── 2. VALIDAR DATOS ENTRANTES ──────────────────────────────────────────
        $request->validate([
            'client_name'  => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'plan'         => 'nullable|string',
            'days'         => 'nullable|integer|min:1|max:3650',
        ]);

        $clientName  = trim($request->input('client_name'));
        $clientEmail = trim($request->input('client_email'));
        $plan        = $request->input('plan', 'Estándar');
        $days        = (int) $request->input('days', 30); // Por defecto 30 días
        $expiresAt   = now()->addDays($days);

        // ── 3. GENERAR KEY ÚNICA ────────────────────────────────────────────────
        do {
            $key = 'TCD-' . strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (License::where('license_key', $key)->exists());

        $license = License::create([
            'license_key'     => $key,
            'client_name'     => $clientName,
            'client_email'    => $clientEmail,
            'status'          => 'active',
            'notes'           => "Generada automáticamente vía API desde Tienda StreamVzla (Plan: {$plan} | {$days} días)",
            'max_clients'     => null,
            'max_queries_day' => null,
        ]);

        // ── 4. GENERAR CREDENCIALES DEL USUARIO ADMIN/FRANQUICIADO ─────────────
        // Dominio configurable desde el panel de Ajustes
        $domain = Setting::get(Setting::KEY_AUTO_USER_DOMAIN, 'streamvzla.com');

        // Crear username limpio desde el primer nombre (sin caracteres raros)
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $clientName)[0]));
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }

        // Si el username ya existe, agregar número: jesus2, jesus3, etc.
        $username = $baseUsername;
        $counter  = 2;
        while (User::where('name', $username)->orWhere('email', "{$username}@{$domain}")->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $userEmail    = "{$username}@{$domain}";
        $userPassword = $this->generateSecurePassword();

        // ── 5. CREAR EL USUARIO EN LA BASE DE DATOS ────────────────────────────
        $user = User::create([
            'name'                  => $username,
            'email'                 => $userEmail,
            'password'              => Hash::make($userPassword),
            'role'                  => 'admin',
            'email_verified_at'     => now(),
            'subscription_ends_at'  => $expiresAt,  // Vencimiento automático
        ]);

        // ── 6. LOG DE CONFIRMACIÓN ─────────────────────────────────────────────
        // El correo de bienvenida NO se envía desde aquí.
        // La tienda StreamVzla recibe las credenciales en la respuesta y es
        // ella quien envía el correo al cliente con su propio SMTP.
        Log::info("BotCodigo: Franquicia creada | KEY={$key} | Usuario={$userEmail} | Vence={$expiresAt->toDateString()} | Para={$clientEmail}");

        // ── 7. RESPONDER A LA TIENDA ────────────────────────────────────────────
        return response()->json([
            'success'       => true,
            'license_key'   => $license->license_key,
            'license_id'    => $license->id,
            'status'        => $license->status,
            'user_email'    => $userEmail,
            'user_password' => $userPassword,
            'user_name'     => $username,
            'panel_url'     => config('app.url') . '/login',
            'expires_at'    => $expiresAt->toDateString(),   // Fecha exacta de vencimiento
            'days'          => $days,                         // Días comprados
            'message'       => "Franquicia activada por {$days} días. Vence el {$expiresAt->toDateString()}.",
        ]);
    }

    /**
     * Genera una contraseña segura de 12 caracteres con mayúsculas, números y símbolos.
     */
    private function generateSecurePassword(): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghjkmnpqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '@#$!';

        $password  = $upper[random_int(0, strlen($upper) - 1)];
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];

        return str_shuffle($password);
    }

    /**
     * Renueva la membresía de un franquiciado existente sumando días desde hoy
     * (o desde la fecha de vencimiento actual si aún no ha vencido).
     * Llamado por la tienda StreamVzla cuando el cliente paga la renovación.
     */
    public function renew(Request $request)
    {
        // ── 1. VERIFICAR SECRETO ────────────────────────────────────────────────
        $secret = $request->header('X-BotCodigo-Secret') ?: $request->input('secret');
        $expectedSecret = config('services.botcodigo.webhook_secret', env('BOTCODIGO_WEBHOOK_SECRET', 'streamvzla_auto_license_secret_2026'));

        if (!$secret || $secret !== $expectedSecret) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
        }

        // ── 2. VALIDAR ──────────────────────────────────────────────────────────
        $request->validate([
            'license_key' => 'required|string',
            'days'        => 'required|integer|min:1|max:3650',
        ]);

        $licenseKey = trim($request->input('license_key'));
        $days       = (int) $request->input('days');

        // ── 3. BUSCAR LA LICENCIA ───────────────────────────────────────────────
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return response()->json(['success' => false, 'message' => 'Licencia no encontrada.'], 404);
        }

        // ── 4. BUSCAR EL USUARIO POR EMAIL DEL CLIENTE ─────────────────────────
        $user = User::where('email', 'like', '%@%')
            ->where('role', 'admin')
            ->whereHas('licenses', fn($q) => $q->where('license_key', $licenseKey))
            ->first();

        // Si no hay relación directa, buscar por client_email de la licencia
        if (!$user) {
            $domain = Setting::get(Setting::KEY_AUTO_USER_DOMAIN, 'tu-codigo.com');
            $baseName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $license->client_name)[0]));
            $user = User::where('email', 'like', "%@{$domain}")
                        ->where('name', $baseName)
                        ->first();
        }

        // ── 5. CALCULAR NUEVA FECHA DE VENCIMIENTO ──────────────────────────────
        // Si aún no venció → extender desde la fecha actual de vencimiento
        // Si ya venció → extender desde hoy (reactivación)
        $currentExpiry = $user?->subscription_ends_at;

        if ($currentExpiry && $currentExpiry->isFuture()) {
            $newExpiry = $currentExpiry->addDays($days);
        } else {
            $newExpiry = now()->addDays($days);
        }

        // ── 6. ACTUALIZAR USUARIO Y LICENCIA ────────────────────────────────────
        if ($user) {
            $user->subscription_ends_at = $newExpiry;
            $user->save();
        }

        $license->status = 'active';
        $license->notes  = $license->notes . " | Renovada +{$days}d el " . now()->toDateString();
        $license->save();

        Log::info("BotCodigo: Licencia renovada | KEY={$licenseKey} | +{$days} días | Nueva fecha={$newExpiry->toDateString()}");

        return response()->json([
            'success'    => true,
            'license_key'=> $licenseKey,
            'days_added' => $days,
            'expires_at' => $newExpiry->toDateString(),
            'message'    => "Membresía renovada por {$days} días. Nuevo vencimiento: {$newExpiry->toDateString()}.",
        ]);
    }

    /**
     * Suspende un franquiciado cuando su membresía vence o el cliente cancela.
     * Llamado por la tienda StreamVzla cuando detecta el vencimiento.
     */
    public function suspend(Request $request)
    {
        // ── 1. VERIFICAR SECRETO ────────────────────────────────────────────────
        $secret = $request->header('X-BotCodigo-Secret') ?: $request->input('secret');
        $expectedSecret = config('services.botcodigo.webhook_secret', env('BOTCODIGO_WEBHOOK_SECRET', 'streamvzla_auto_license_secret_2026'));

        if (!$secret || $secret !== $expectedSecret) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
        }

        // ── 2. VALIDAR ──────────────────────────────────────────────────────────
        $request->validate([
            'license_key' => 'required|string',
        ]);

        $license = License::where('license_key', trim($request->input('license_key')))->first();

        if (!$license) {
            return response()->json(['success' => false, 'message' => 'Licencia no encontrada.'], 404);
        }

        // ── 3. SUSPENDER ────────────────────────────────────────────────────────
        $license->status = 'suspended';
        $license->save();

        // Bloquear el usuario asociado
        $domain   = Setting::get(Setting::KEY_AUTO_USER_DOMAIN, 'tu-codigo.com');
        $baseName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $license->client_name)[0]));
        $user     = User::where('email', 'like', "%@{$domain}")->where('name', $baseName)->first();

        if ($user) {
            $user->subscription_ends_at = now()->subDay(); // Marcarlo como vencido
            $user->save();
        }

        Log::info("BotCodigo: Licencia suspendida | KEY={$request->input('license_key')}");

        return response()->json([
            'success'     => true,
            'license_key' => $license->license_key,
            'status'      => 'suspended',
            'message'     => 'Franquicia suspendida correctamente.',
        ]);
    }
}
