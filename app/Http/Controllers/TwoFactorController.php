<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    public function enable(Request $request)
    {
        $user = auth('client')->user() ?? auth('web')->user();
        
        if (!$user) {
            abort(403);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $user->two_factor_secret = $secret;
        $user->save();

        // Generar QR Code
        $companyName = \App\Models\Setting::get('site_name', 'NexusCode');
        $companyEmail = $user->email;
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return response()->json([
            'success' => true,
            'qrCode' => $qrCodeSvg,
            'secret' => $secret
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $user = auth('client')->user() ?? auth('web')->user();
        if (!$user || !$user->two_factor_secret) {
            return response()->json(['success' => false, 'message' => '2FA no está en proceso de configuración'], 400);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            $user->two_factor_confirmed_at = now();
            $user->save();
            return response()->json(['success' => true, 'message' => '2FA habilitado correctamente.']);
        }

        return response()->json(['success' => false, 'message' => 'El código es incorrecto.'], 400);
    }

    public function disable(Request $request)
    {
        $user = auth('client')->user() ?? auth('web')->user();
        if (!$user) abort(403);

        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return response()->json(['success' => true, 'message' => '2FA deshabilitado.']);
    }

    public function showVerifyForm(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.2fa-verify');
    }

    public function verifyLogin(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $userId = $request->session()->get('2fa_user_id');
        $guard = $request->session()->get('2fa_guard');
        $remember = $request->session()->get('2fa_remember');

        if (!$userId || !$guard) {
            return redirect()->route('login')->withErrors(['code' => 'Sesión expirada.']);
        }

        if ($guard === 'client') {
            $user = \App\Models\Client::find($userId);
            $redirectUrl = route('client.dashboard');
        } else {
            $user = \App\Models\User::find($userId);
            $redirectUrl = route('admin.dashboard');
        }

        if (!$user || !$user->two_factor_secret) {
            return redirect()->route('login')->withErrors(['code' => 'Error de configuración.']);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            \Illuminate\Support\Facades\Auth::guard($guard)->login($user, $remember);
            $user->update(['last_login_at' => now()]);
            
            $request->session()->forget(['2fa_user_id', '2fa_guard', '2fa_remember']);
            $request->session()->regenerate();

            return redirect()->intended($redirectUrl);
        }

        return back()->withErrors(['code' => 'El código proporcionado es incorrecto.']);
    }
}
