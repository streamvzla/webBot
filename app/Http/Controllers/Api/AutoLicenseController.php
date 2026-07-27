<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AutoLicenseController extends Controller
{
    /**
     * Crea una nueva licencia automáticamente solicitada desde StreamVzla / tiendaOnline.
     */
    public function create(Request $request)
    {
        // Validar secreto de seguridad para evitar creaciones no autorizadas
        $secret = $request->header('X-BotCodigo-Secret') ?: $request->input('secret');
        $expectedSecret = config('services.botcodigo.webhook_secret', env('BOTCODIGO_WEBHOOK_SECRET', 'streamvzla_auto_license_secret_2026'));

        if (!$secret || $secret !== $expectedSecret) {
            Log::warning("Intento fallido de creación automática de licencia en BotCodigo desde IP: " . $request->ip());
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Token de seguridad inválido.'
            ], 401);
        }

        $request->validate([
            'client_name'  => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'plan'         => 'nullable|string',
        ]);

        // Generar clave única en formato oficial TCD-XXXX-YYYY-ZZZZ
        do {
            $key = 'TCD-' . strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (License::where('license_key', $key)->exists());

        $license = License::create([
            'license_key'     => $key,
            'client_name'     => $request->input('client_name', 'Cliente StreamVzla'),
            'client_email'    => $request->input('client_email'),
            'status'          => 'active',
            'notes'           => 'Generada automáticamente vía API desde Tienda StreamVzla (Orden / Plan: ' . $request->input('plan', 'Estándar') . ')',
            'max_clients'     => null,
            'max_queries_day' => null,
        ]);

        Log::info("BotCodigo: Licencia automática creada #{$key} para " . $request->input('client_email', 'N/A'));

        return response()->json([
            'success'     => true,
            'license_key' => $license->license_key,
            'license_id'  => $license->id,
            'status'      => $license->status,
        ]);
    }
}
