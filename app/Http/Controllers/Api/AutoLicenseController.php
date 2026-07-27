<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\FranchiseWelcomeMail;
use App\Models\License;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        ]);

        $clientName  = trim($request->input('client_name'));
        $clientEmail = trim($request->input('client_email'));
        $plan        = $request->input('plan', 'Estándar');

        // ── 3. GENERAR KEY ÚNICA ────────────────────────────────────────────────
        do {
            $key = 'TCD-' . strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (License::where('license_key', $key)->exists());

        $license = License::create([
            'license_key'     => $key,
            'client_name'     => $clientName,
            'client_email'    => $clientEmail,
            'status'          => 'active',
            'notes'           => "Generada automáticamente vía API desde Tienda StreamVzla (Plan: {$plan})",
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
            'name'              => $username,
            'email'             => $userEmail,
            'password'          => Hash::make($userPassword),
            'role'              => 'admin',   // Admin = Franquiciado con panel completo
            'email_verified_at' => now(),
        ]);

        // ── 6. ENVIAR CORREO DE BIENVENIDA CON CREDENCIALES ────────────────────
        $siteName = Setting::get(Setting::KEY_SITE_NAME, config('app.name', 'BotCodigo'));
        $panelUrl = config('app.url') . '/login';

        try {
            Mail::to($clientEmail)->send(new FranchiseWelcomeMail(
                clientName:   $clientName,
                userEmail:    $userEmail,
                userPassword: $userPassword,
                panelUrl:     $panelUrl,
                siteName:     $siteName,
            ));
            Log::info("BotCodigo: Correo de bienvenida enviado a {$clientEmail} para usuario {$userEmail}");
        } catch (\Throwable $e) {
            // No cancelar si el correo falla — la cuenta ya fue creada
            Log::error("BotCodigo: No se pudo enviar correo de bienvenida a {$clientEmail}: " . $e->getMessage());
        }

        Log::info("BotCodigo: Franquicia creada | KEY={$key} | Usuario={$userEmail} | Para={$clientEmail}");

        // ── 7. RESPONDER A LA TIENDA ────────────────────────────────────────────
        return response()->json([
            'success'       => true,
            'license_key'   => $license->license_key,
            'license_id'    => $license->id,
            'status'        => $license->status,
            'user_email'    => $userEmail,
            'user_password' => $userPassword,
            'user_name'     => $username,
            'panel_url'     => $panelUrl,
            'message'       => "Franquicia activada. Credenciales enviadas a {$clientEmail}.",
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
}
