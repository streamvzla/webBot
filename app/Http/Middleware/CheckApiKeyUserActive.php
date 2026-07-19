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
            // Verificamos que sea de tipo Administrador/Usuario y esté activo
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. The API key owner account is disabled.'
                ], 401);
            }
        }

        return $next($request);
    }
}
