<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Update;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    public function check(Request $request)
    {
        // El cliente consulta si hay actualizaciones enviando su versión actual
        // Podríamos registrar qué cliente consulta, pero lo haremos anónimo por velocidad
        
        $latestUpdate = Update::where('is_active', true)->orderBy('id', 'desc')->first();

        if (!$latestUpdate) {
            return response()->json([
                'success' => true,
                'has_update' => false,
                'message' => 'No hay actualizaciones disponibles.'
            ]);
        }

        return response()->json([
            'success' => true,
            'has_update' => true,
            'version' => $latestUpdate->version,
            'release_notes' => $latestUpdate->release_notes,
            'created_at' => $latestUpdate->created_at->format('Y-m-d')
        ]);
    }

    public function download(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
            'version' => 'required|string'
        ]);

        $licenseKey = $request->input('license_key');
        $domain = $request->input('domain');

        // Limpiar dominio
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/');

        $license = License::where('license_key', $licenseKey)->first();

        // Validaciones estrictas de seguridad (Killswitch/Anti-piratería)
        if (!$license || $license->status !== 'active' || strtolower($license->domain) !== strtolower($domain)) {
            return response()->json(['success' => false, 'message' => 'Acceso Denegado. Licencia inválida o revocada.'], 403);
        }

        $update = Update::where('version', $request->version)->where('is_active', true)->first();

        if (!$update || !file_exists(storage_path('app/' . $update->zip_path))) {
            return response()->json(['success' => false, 'message' => 'El archivo de actualización no se encuentra en el servidor.'], 404);
        }

        return response()->download(storage_path('app/' . $update->zip_path), 'update_' . $update->version . '.zip');
    }
}
