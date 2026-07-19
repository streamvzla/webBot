<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFranchiseSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 1. If not authenticated or is super admin, let them pass
        if (!$user || $user->is_superadmin) {
            return $next($request);
        }

        // Get the root franchise user (the one who actually holds the subscription)
        $rootUser = $user->getRootFranchise();

        // 2. If subscription expired (and not in grace period), activate read-only mode
        if ($rootUser->isSubscriptionExpired()) {
            // Permite acceso a logout
            if ($request->routeIs('logout')) {
                return $next($request);
            }
            
            // Si la ruta es la antigua página de expirado, la redirigimos al dashboard
            if ($request->routeIs('admin.subscription.expired')) {
                return redirect()->route('admin.dashboard');
            }

            // Inyectar flag de solo lectura para la vista
            session()->now('read_only_mode', true);

            // Bloquear acciones de escritura (POST, PUT, PATCH, DELETE)
            if (!$request->isMethod('GET') && !$request->isMethod('HEAD') && !$request->isMethod('OPTIONS')) {
                if ($request->expectsJson() || $request->header('X-Livewire')) {
                    return response()->json([
                        'message' => 'Modo de Solo Lectura: Tu suscripción ha expirado. Las acciones están deshabilitadas.'
                    ], 403);
                }
                
                return back()->with('error', 'Modo de Solo Lectura: Tu suscripción ha expirado. No puedes realizar cambios.');
            }
            
            // Permitir navegación en GET (Solo lectura)
            return $next($request);
        }

        // 3. If in grace period, flash a warning so the view can display the banner
        if ($rootUser->isInGracePeriod()) {
            session()->now('subscription_grace_warning', true);
            session()->now('subscription_grace_days_left', $rootUser->getDaysUntilExpiration() + $rootUser->grace_days);
        }

        return $next($request);
    }
}
