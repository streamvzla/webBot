<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClientAuthController extends Controller
{
    /**
     * Mostrar formulario de login para clientes.
     */
    public function showLogin()
    {
        return view('client.login');
    }

    /**
     * Procesar login de cliente.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('client')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('client.dashboard'))
                ->with('success', 'Bienvenido de nuevo.');
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales no son válidas.'],
        ]);
    }

    /**
     * Cerrar sesión de cliente.
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login')
            ->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Mostrar dashboard del cliente.
     */
    public function dashboard()
    {
        $client = Auth::guard('client')->user();

        // Contact settings
        $contactTelegram = Setting::get(Setting::KEY_TELEGRAM_URL);
        $contactWhatsapp = Setting::get(Setting::KEY_WHATSAPP_URL);
        $webUrl = Setting::get(Setting::KEY_WEB_URL);
        $whatsappMessage = Setting::get(Setting::KEY_WHATSAPP_MESSAGE, 'Hola, necesito ayuda con el sistema de códigos');

        return view('client.dashboard', compact('client', 'contactTelegram', 'contactWhatsapp', 'webUrl', 'whatsappMessage'));
    }

    /**
     * Mostrar perfil del cliente.
     */
    public function profile()
    {
        $client = Auth::guard('client')->user();

        return view('client.profile', compact('client'));
    }

    /**
     * Actualizar perfil del cliente.
     */
    public function updateProfile(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $client->name = $validated['name'];

        if (!empty($validated['password'])) {
            $client->password = Hash::make($validated['password']);
        }

        $client->update();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Obtener estado del rate limiting.
     */
    public function getLimitStatus()
    {
        $client = Auth::guard('client')->user();
        $limiter = new \App\Services\QueryLimiter($client);

        return response()->json([
            'limit_status' => $limiter->getLimitStatus(),
        ]);
    }
}
