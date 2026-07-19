<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Setting;
use App\Mail\EmailChangeVerification;
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
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $attemptCredentials = array_merge($credentials, ['is_active' => true]);

        if (Auth::guard('client')->attempt($attemptCredentials)) {
            $request->session()->regenerate();

            $client = Auth::guard('client')->user();
            $client->update(['last_login_at' => now()]);

            return redirect()->intended(route('client.dashboard'))
                ->with('success', 'Bienvenido de nuevo.');
        }

        $client = Client::where('email', $credentials['email'])->first();
        if ($client && !$client->is_active) {
            return back()->with('account_suspended', 'Contacta a tu proveedor, no podemos dejarte entrar.');
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
        $user   = $client->user;

        $contactTelegram = ($user && $user->telegram) ? $user->telegram : Setting::get(Setting::KEY_TELEGRAM_URL);
        $contactWhatsapp = ($user && $user->whatsapp) ? $user->whatsapp : Setting::get(Setting::KEY_WHATSAPP_URL);
        $webUrl          = ($user && $user->website)  ? $user->website  : Setting::get(Setting::KEY_WEB_URL);
        $whatsappMessage = Setting::get(Setting::KEY_WHATSAPP_MESSAGE, 'Hola, necesito ayuda con el sistema de códigos');

        $recentWarranties = $client->warrantyRequests()->with('platform')->latest()->limit(3)->get();
        $clientPlatforms  = $client->platforms()->where('is_active', true)->get();
        $allowedEmailCount = $client->allowedEmails()->count();

        return view('client.dashboard', compact(
            'client', 'contactTelegram', 'contactWhatsapp',
            'webUrl', 'whatsappMessage',
            'recentWarranties', 'clientPlatforms', 'allowedEmailCount'
        ));
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
     * Actualizar perfil del cliente (nombre, teléfono, contraseña).
     */
    public function updateProfile(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $client->name  = $validated['name'];
        $client->phone = $validated['phone'] ?? null;

        if (!empty($validated['password'])) {
            $client->password = Hash::make($validated['password']);
        }

        $client->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Subir y comprimir avatar del cliente.
     * Usa GD para redimensionar a máx 200x200px y JPEG al 75% — archivo < 20KB.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $client = Auth::guard('client')->user();
        $file   = $request->file('avatar');
        $mime   = $file->getMimeType();

        // Crear imagen GD desde el archivo subido
        $src = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'gif')  => imagecreatefromgif($file->getRealPath()),
            str_contains($mime, 'webp') => imagecreatefromwebp($file->getRealPath()),
            default                     => imagecreatefromjpeg($file->getRealPath()),
        };

        if (!$src) {
            return back()->with('error', 'No se pudo procesar la imagen. Intenta con otro formato.');
        }

        // Redimensionar manteniendo proporción — máx 200×200
        $origW  = imagesx($src);
        $origH  = imagesy($src);
        $maxDim = 200;
        $ratio  = min($maxDim / $origW, $maxDim / $origH, 1);
        $newW   = (int) round($origW * $ratio);
        $newH   = (int) round($origH * $ratio);

        $dst = imagecreatetruecolor($newW, $newH);

        // Preservar transparencia en PNG/GIF
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefill($dst, 0, 0, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        // Mover a public/avatars en lugar de storage para evitar problemas de symlink
        $filename = $client->id . '_' . time() . '.jpg';
        $destinationPath = public_path('avatars');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Guardar con GD directamente
        imagejpeg($dst, $destinationPath . '/' . $filename, 75);
        imagedestroy($dst);

        // Borrar avatar anterior si existe
        if ($client->avatar && file_exists(public_path($client->avatar))) {
            @unlink(public_path($client->avatar));
        }

        $client->avatar = 'avatars/' . $filename;
        $client->save();

        return back()->with('success', 'Foto de perfil actualizada correctamente.');
    }

    /**
     * Solicitar cambio de email — envía verificación al nuevo correo.
     */
    public function requestEmailChange(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'new_email' => [
                'required', 'email', 'max:255',
                'different:' . $client->email,
                'unique:clients,email,' . $client->id,
            ],
        ], [
            'new_email.different' => 'El nuevo correo debe ser diferente al actual.',
            'new_email.unique'    => 'Este correo ya está registrado en otra cuenta.',
        ]);

        // Generar token seguro y guardarlo hasheado
        $token   = Str::random(64);
        $expires = now()->addMinutes(60);

        $client->update([
            'pending_email'                 => $request->new_email,
            'email_change_token'            => hash('sha256', $token),
            'email_change_token_expires_at' => $expires,
        ]);

        $verificationUrl = route('client.email-change.verify', ['token' => $token]);

        Mail::to($request->new_email)->send(
            new EmailChangeVerification($client->name, $request->new_email, $verificationUrl)
        );

        return back()->with('email_change_sent', 'Enviamos un enlace de verificación a ' . $request->new_email . '. Tienes 60 minutos para confirmarlo.');
    }

    /**
     * Confirmar cambio de email via token (no requiere auth — clic desde cualquier dispositivo).
     */
    public function confirmEmailChange(Request $request, string $token)
    {
        $hashedToken = hash('sha256', $token);

        $client = Client::where('email_change_token', $hashedToken)
            ->where('email_change_token_expires_at', '>', now())
            ->first();

        if (!$client) {
            return redirect()->route('client.login')
                ->with('error', 'El enlace de verificación es inválido o ha expirado. Solicita uno nuevo desde tu perfil.');
        }

        $newEmail = $client->pending_email;

        $client->update([
            'email'                         => $newEmail,
            'pending_email'                 => null,
            'email_change_token'            => null,
            'email_change_token_expires_at' => null,
        ]);

        return redirect()->route('client.profile')
            ->with('success', '¡Correo actualizado exitosamente a ' . $newEmail . '!');
    }

    /**
     * Obtener estado del rate limiting.
     */
    public function getLimitStatus()
    {
        $client  = Auth::guard('client')->user();
        $limiter = new \App\Services\QueryLimiter($client);

        return response()->json([
            'limit_status' => $limiter->getLimitStatus(),
        ]);
    }
}
