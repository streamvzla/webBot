<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Services\QueryLimiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->user('client');

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Debes iniciar sesión.',
            ], 401);
        }

        $limiter = new QueryLimiter($client);

        if (!$limiter->canMakeQuery()) {
            $status = $limiter->getLimitStatus();

            return response()->json([
                'success' => false,
                'message' => 'Límite de consultas alcanzado.',
                'limit' => [
                    'wait_seconds' => $status['seconds_until_next'],
                    'formatted_time' => $status['formatted_time'],
                    'remaining_today' => $status['remaining_today'],
                    'max_daily' => $status['max_daily'],
                ],
            ], 429);
        }

        return $next($request);
    }
}
