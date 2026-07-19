<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin puede acceder a todo
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Verificar si el rol del usuario está permitido
        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'No tienes autorización para acceder a esta sección.');
        }

        return $next($request);
    }
}
