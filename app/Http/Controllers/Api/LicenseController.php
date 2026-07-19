<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function validateLicense(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string'
        ]);

        $licenseKey = $request->input('license_key');
        $domain = $request->input('domain');

        // Clean domain (remove http://, https://, www., trailing slash)
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/');

        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia inválida o no existe.'
            ], 404);
        }

        if ($license->status === 'suspended') {
            return response()->json([
                'success' => false,
                'message' => 'Esta licencia se encuentra suspendida.'
            ], 403);
        }

        if ($license->status === 'revoked') {
            return response()->json([
                'success' => false,
                'message' => 'Esta licencia ha sido revocada.'
            ], 403);
        }

        // Domain binding logic
        if (empty($license->domain)) {
            // First time use -> bind to this domain
            $license->domain = $domain;
            $license->save();
        } else {
            // Check if domain matches
            if (strtolower($license->domain) !== strtolower($domain)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta licencia ya está registrada para otro dominio.'
                ], 403);
            }
        }

        // Generate JWT (Tatuaje Digital)
        $secretKey = env('JWT_LICENSE_SECRET', 'tu_clave_secreta_super_segura_aqui');
        
        $payload = [
            'iss' => url('/'),            // Issuer
            'aud' => $domain,             // Audience (The client's domain)
            'iat' => time(),              // Issued at
            'nbf' => time(),              // Not before
            // We can add expiration if we want, but they are lifetime licenses
            'data' => [
                'license_key' => $license->license_key,
                'status' => 'active',
                'type' => 'vitalicia'
            ]
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        return response()->json([
            'success' => true,
            'message' => '¡Licencia Activada Correctamente!',
            'token' => $jwt,
            'domain' => $domain
        ]);
    }

    public function heartbeat(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string'
        ]);

        $licenseKey = $request->input('license_key');
        $domain = $request->input('domain');

        // Clean domain
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/');

        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'action' => 'revoke',
                'message' => 'Licencia no encontrada.'
            ]);
        }

        if ($license->status === 'suspended' || $license->status === 'revoked') {
            return response()->json([
                'success' => false,
                'action' => 'revoke',
                'message' => 'Licencia suspendida o revocada.'
            ]);
        }

        if (strtolower($license->domain) !== strtolower($domain)) {
            return response()->json([
                'success' => false,
                'action' => 'revoke',
                'message' => 'Dominio no autorizado.'
            ]);
        }

        return response()->json([
            'success' => true,
            'action' => 'keep',
            'message' => 'Licencia activa.'
        ]);
    }
}
