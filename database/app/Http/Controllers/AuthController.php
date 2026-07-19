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
     * Handle login request (supports both client and admin)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'login_type' => ['required', 'in:client,admin'],
        ]);

        $loginType = $credentials['login_type'];

        if ($loginType === 'admin') {
            return $this->loginAdmin($request, $credentials);
        } else {
            return $this->loginClient($request, $credentials);
        }
    }

    /**
     * Handle admin login
     */
    protected function loginAdmin(Request $request, array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no son correctas.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta está desactivada. Contacta al administrador.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        // Update last login
        $user->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Handle client login
     */
    protected function loginClient(Request $request, array $credentials)
    {
        $client = Client::where('email', $credentials['email'])->first();

        if (!$client || !Hash::check($credentials['password'], $client->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no son correctas.'],
            ]);
        }

        if (!$client->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta está desactivada. Contacta al administrador.'],
            ]);
        }

        Auth::guard('client')->login($client, $request->boolean('remember'));

        $client->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->intended(route('client.dashboard'));
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
