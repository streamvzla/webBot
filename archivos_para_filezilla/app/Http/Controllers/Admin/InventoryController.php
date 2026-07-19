<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllowedEmail;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the reseller's inventory.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Obtener todas las cuentas que le pertenecen a este usuario
        $query = AllowedEmail::with(['platform', 'clients' => function($q) {
            $q->whereNull('allowed_email_client.expires_at')
              ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
        }])->where('user_id', $user->id);

        // 2. Aplicar Filtros (si los hay)
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'free') {
                $query->whereDoesntHave('clients', function($q) {
                    $q->whereNull('allowed_email_client.expires_at')
                      ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                });
            } elseif ($request->status === 'assigned') {
                $query->whereHas('clients', function($q) {
                    $q->whereNull('allowed_email_client.expires_at')
                      ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                });
            }
        }

        if ($request->has('expiration') && $request->expiration !== '') {
            $today = now()->startOfDay();
            if ($request->expiration === 'expired') {
                $query->where('expires_at', '<', $today);
            } elseif ($request->expiration === '1_day') {
                $query->whereDate('expires_at', '=', $today->copy()->addDay());
            } elseif ($request->expiration === '2_days') {
                $query->whereDate('expires_at', '=', $today->copy()->addDays(2));
            } elseif ($request->expiration === '3_days') {
                $query->whereDate('expires_at', '=', $today->copy()->addDays(3));
            }
        }

        $inventory = $query->orderBy('expires_at', 'asc')->paginate(20);

        // 3. Stats Globales del Inventario
        $baseQuery = AllowedEmail::where('user_id', $user->id);
        
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'expired' => (clone $baseQuery)->where('expires_at', '<', now()->startOfDay())->count(),
            'free' => (clone $baseQuery)->whereDoesntHave('clients', function($q) {
                $q->whereNull('allowed_email_client.expires_at')
                  ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
            })->count(),
            'assigned' => (clone $baseQuery)->whereHas('clients', function($q) {
                $q->whereNull('allowed_email_client.expires_at')
                  ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
            })->count(),
        ];

        return view('admin.inventory.index', compact('inventory', 'stats'));
    }

    /**
     * Release (Do Not Renew) one or multiple accounts.
     * Returns them to the SuperAdmin pool by clearing user_id and assigned_to.
     */
    public function release(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'email_ids' => 'required|array',
            'email_ids.*' => 'exists:allowed_emails,id'
        ]);

        // Verificar que todas las cuentas seleccionadas pertenezcan al usuario actual
        $emails = AllowedEmail::whereIn('id', $request->email_ids)
            ->where('user_id', $user->id)
            ->get();

        if ($emails->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No se encontraron cuentas válidas para liberar.']);
        }

        foreach ($emails as $email) {
            // Liberar la cuenta:
            // 1. Quitar al revendedor como dueño
            $email->user_id = 1; // Devolver al Super Admin (ID 1)
            $email->assigned_to = null;
            // 2. Como ya no es del revendedor, cortamos las asignaciones de sus clientes
            $email->clients()->detach();
            
            $email->save();
        }

        return response()->json([
            'success' => true, 
            'message' => count($emails) . ' cuentas fueron liberadas exitosamente y devueltas al Administrador.'
        ]);
    }
}
