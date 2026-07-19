<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installed = File::exists(storage_path('installed.lock'));
        
        // Exclude assets and specific internal routes from interception
        if ($request->is('livewire/*') || $request->is('build/*') || $request->is('build/assets/*')) {
            return $next($request);
        }

        if (!$installed) {
            if (!$request->is('install') && !$request->is('install/*')) {
                return redirect()->route('install.step1');
            }
        } else {
            if ($request->is('install') || $request->is('install/*')) {
                return redirect('/');
            }
        }

        return $next($request);
    }
}
