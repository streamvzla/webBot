<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\IpBan;
use Illuminate\Support\Facades\Cache;

class CheckIpBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // 1. Verificar si la IP está baneada
        $ban = IpBan::where('ip_address', $ip)->first();
        if ($ban) {
            if ($ban->expires_at && $ban->expires_at->isPast()) {
                // El ban expiró, lo eliminamos
                $ban->delete();
            } else {
                // Retornar mensaje de baneo
                return response()->json([
                    'success' => false,
                    'message' => 'Tu IP ha sido bloqueada por el Sistema Anti-Spam por realizar demasiadas solicitudes en poco tiempo. Contacta a soporte para ser desbaneado.'
                ], 403);
            }
        }

        // 2. Lógica Anti-Spam (Ej: más de 2 solicitudes en 5 segundos)
        $cacheKey = 'anti_spam_ip_' . $ip;
        $requests = Cache::get($cacheKey, 0);

        if ($requests >= 2) {
            // Banear la IP por 1 hora
            IpBan::create([
                'ip_address' => $ip,
                'client_id' => auth('client')->id() ?? null,
                'reason' => 'Demasiados clics rápidos (Anti-Spam activado)',
                'expires_at' => now()->addHour()
            ]);
            Cache::forget($cacheKey);

            return response()->json([
                'success' => false,
                'message' => 'Has sido baneado automáticamente por exceder el límite de clics rápidos.'
            ], 403);
        }

        Cache::put($cacheKey, $requests + 1, now()->addSeconds(5));

        return $next($request);
    }
}
