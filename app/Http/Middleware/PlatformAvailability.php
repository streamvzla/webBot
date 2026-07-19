<?php

namespace App\Http\Middleware;

use App\Models\Platform;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlatformAvailability
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $platformId = $request->route('platformId') ?? $request->input('platform_id');

        if (!$platformId) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no especificada.',
            ], 400);
        }

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
                'message' => "La plataforma {$platform->name} no está disponible actualmente.",
            ], 503);
        }

        // Adjuntar plataforma a la request para uso posterior
        $request->merge(['platform' => $platform]);

        return $next($request);
    }
}
