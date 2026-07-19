<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Mostrar página pública de consulta (sin autenticación).
     */
    public function index(): View
    {
        $platforms = Platform::where('is_active', true)->get();
        $setting = Setting::first();

        // Obtener datos del email temporal de la sesión si existen
        $emailBody = Session::get('email_body');
        $emailReceivedAt = Session::get('email_received_at');
        $tempCodeExpiry = Session::get('temp_code_expiry');
        $emailIsHtml = Session::get('email_is_html', false);

        return view('public.query', compact('platforms', 'setting', 'emailBody', 'emailReceivedAt', 'tempCodeExpiry', 'emailIsHtml'));
    }

    /**
     * Redirigir según autenticación.
     */
    public function redirect()
    {
        // Check client guard first (for clients who also have admin access)
        try {
            if (Auth::guard('client')->check()) {
                return redirect()->route('client.dashboard');
            }
        } catch (\Exception $e) {
            // Guard not defined, continue
        }

        // Then check web guard (for admins)
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        // Otherwise, show public query page
        return redirect()->route('home');
    }
}
