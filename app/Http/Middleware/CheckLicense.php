<?php

namespace App\Http\Middleware;

use App\Models\License;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

/**
 * CheckLicense — Middleware de protección de licencia
 *
 * Valida que el sistema tiene una licencia activa y que el dominio
 * actual coincide con el dominio registrado en la licencia.
 *
 * Flujo:
 *   1. Lee SYSTEM_LICENSE_KEY del .env
 *   2. Busca la licencia en la BD
 *   3. Si no existe o está revocada/suspendida → muestra pantalla de error
 *   4. Si el dominio no coincide → muestra pantalla de error
 *   5. Si es la primera activación → auto-vincula el dominio y deja pasar
 *   6. Si todo OK → actualiza last_verified_at y deja pasar
 */
class CheckLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        // Excluir el instalador y assets
        if ($request->is('install') || $request->is('install/*') || $request->is('livewire/*') || $request->is('build/*')) {
            return $next($request);
        }

        $licenseKey = config('app.license_key') ?: env('SYSTEM_LICENSE_KEY');

        // Si no hay clave configurada, mostrar pantalla de activación
        if (!$licenseKey) {
            return $this->licenseError(
                $request,
                'Sin Licencia',
                'Este sistema no tiene una clave de licencia configurada.',
                'Agrega SYSTEM_LICENSE_KEY=TCD-XXXX-XXXX-XXXX en tu archivo .env y reinicia el servidor.'
            );
        }

        $domain = $request->getHost();

        $result = License::validate($licenseKey, $domain);

        if (!$result['valid']) {
            Log::warning('License check failed', [
                'key'    => $licenseKey,
                'domain' => $domain,
                'reason' => $result['reason'],
                'ip'     => $request->ip(),
            ]);

            return $this->licenseError(
                $request,
                'Licencia Inválida',
                $result['reason'],
                'Contacta al proveedor del sistema para regularizar tu licencia.'
            );
        }

        return $next($request);
    }

    /**
     * Renderizar la pantalla de error de licencia.
     * Para peticiones JSON/AJAX devuelve JSON.
     */
    private function licenseError(Request $request, string $title, string $message, string $hint): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error'   => $title,
                'message' => $message,
            ], 403);
        }

        // Renderizar vista de error de licencia
        return response(view('errors.license', compact('title', 'message', 'hint')), 403);
    }
}
