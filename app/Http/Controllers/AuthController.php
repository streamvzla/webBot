<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request (Smart Login: automatically detects if user is client or admin)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // 1. Intentar iniciar sesión como Cliente
        $client = Client::where('email', $credentials['email'])->first();

        if ($client) {
            if (!Hash::check($credentials['password'], $client->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas no son correctas.'],
                ]);
            }

            if (!$client->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Tu cuenta está desactivada. Contacta al administrador.'],
                ]);
            }

            if ($client->two_factor_confirmed_at) {
                $request->session()->put('2fa_user_id', $client->id);
                $request->session()->put('2fa_guard', 'client');
                $request->session()->put('2fa_remember', $remember);
                return redirect()->route('2fa.verify');
            }

            Auth::guard('client')->login($client, $remember);
            $client->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            return redirect()->intended(route('client.dashboard'));
        }

        // 2. Si no es cliente, intentar como Administrador/User
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if (!Hash::check($credentials['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas no son correctas.'],
                ]);
            }

            if (!$user->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Tu cuenta está desactivada. Contacta al administrador.'],
                ]);
            }

            if ($user->two_factor_confirmed_at) {
                $request->session()->put('2fa_user_id', $user->id);
                $request->session()->put('2fa_guard', 'web');
                $request->session()->put('2fa_remember', $remember);
                return redirect()->route('2fa.verify');
            }

            Auth::login($user, $remember);
            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // 3. No existe en ninguna tabla
        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas no son correctas.'],
        ]);
    }

    /**
     * Logout user (handles both admin and client)
     */
    public function logout(Request $request)
    {
        if (Auth::guard('client')->check()) {
            Auth::guard('client')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
