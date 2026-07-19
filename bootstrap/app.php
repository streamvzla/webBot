<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\CheckInstallation::class,
            // CheckLicense no aplica en la instalación del superadmin (dueño del sistema)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Cuando el token CSRF expira (error 419), redirigir al login con mensaje claro
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu sesión ha expirado. Por favor recarga la página.'
                ], 419);
            }

            return redirect()
                ->route('login')
                ->withInput($request->except('_token', 'password'))
                ->withErrors([
                    'email' => 'Tu sesión expiró por inactividad. Por favor ingresa de nuevo.',
                ]);
        });
    })->create();

