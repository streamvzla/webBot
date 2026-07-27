<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKeyUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si hay un usuario autenticado por Sanctum (API Token)
        if ($user) {
            // Verificamos que esté activo
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado. La cuenta propietaria del API key está desactivada.'
                ], 401);
            }

            // Verificamos que su membresía/suscripción no haya vencido
            if ($user->isSubscriptionExpired()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'SUBSCRIPTION_EXPIRED',
                    'message' => 'Membresía vencida. Tu suscripción a BotCodigo expiró el ' . $user->subscription_ends_at->toDateString() . '. Por favor, renueva en la tienda online.'
                ], 403);
            }
        }

        return $next($request);
    }
}
