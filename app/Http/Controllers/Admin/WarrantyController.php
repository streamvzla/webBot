<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyRequest;
use App\Models\AllowedEmail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarrantyController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = WarrantyRequest::with(['client', 'platform'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC")
            ->orderBy('created_at', 'desc');
            
        if ($user->id !== 1) {
            $descendants = $user->getDescendantsIds();
            $query->whereHas('client', function($q) use ($descendants) {
                $q->whereIn('user_id', $descendants);
            });
        }

        $warranties = $query->get();

        // Obtener clientes de este revendedor/admin que tengan cuentas de correo asignadas
        $clientsQuery = \App\Models\Client::with(['allowedEmails' => function($q) {
            $q->whereNull('allowed_email_client.expires_at')
              ->orWhereDate('allowed_email_client.expires_at', '>=', \Carbon\Carbon::now()->toDateString());
        }]);
        
        if ($user->id !== 1) {
            $clientsQuery->whereIn('user_id', $user->getDescendantsIds());
        }
        $clients = $clientsQuery->get()->filter(function($client) {
            return $client->allowedEmails->count() > 0;
        });

        // Obtener correos que pertenecen al usuario (admin/revendedor) pero que NO están asignados a ningún cliente activo
        $unassignedEmails = \App\Models\AllowedEmail::where('user_id', $user->id)
            ->whereDoesntHave('clients', function($q) {
                $q->whereNull('allowed_email_client.expires_at')
                  ->orWhereDate('allowed_email_client.expires_at', '>=', \Carbon\Carbon::now()->toDateString());
            })
            ->pluck('email')
            ->toArray();

        return view('admin.warranties.index', compact('warranties', 'clients', 'unassignedEmails'));
    }

    public function show(WarrantyRequest $warranty)
    {
        $user = auth()->user();
        if ($user->id !== 1) {
            $descendants = $user->getDescendantsIds();
            if (!in_array($warranty->client->user_id, $descendants)) {
                abort(403, 'No autorizado para ver esta garantía.');
            }
        }
        return view('admin.warranties.show', compact('warranty'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'report_type' => 'required|in:client,personal',
            'client_id' => 'required_if:report_type,client|nullable|exists:clients,id',
            'old_email' => 'required|email',
            'type' => 'required|in:minor_issue,replacement',
            'reason' => 'required|string|max:1000',
        ]);

        $clientId = null;
        if ($request->report_type === 'client') {
            $client = \App\Models\Client::findOrFail($request->client_id);
            if ($user->id !== 1) {
                $descendants = $user->getDescendantsIds();
                if (!in_array($client->user_id, $descendants)) {
                    abort(403, 'No autorizado para reportar garantía de este cliente.');
                }
            }
            $clientId = $client->id;
        }

        // Obtener platform_id basado en el correo
        $allowedEmail = AllowedEmail::where('email', $request->old_email)->first();
        if ($allowedEmail && $allowedEmail->paused_at == null) {
            // Pausar el tiempo del correo
            $allowedEmail->update(['paused_at' => Carbon::now()]);
        }

        WarrantyRequest::create([
            'client_id' => $clientId,
            'old_email' => $request->old_email,
            'platform_id' => $allowedEmail ? $allowedEmail->platform_id : null,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return redirect()->route('admin.warranties.index')->with('success', 'Garantía reportada correctamente. El tiempo de la cuenta ha sido pausado.');
    }

    public function update(Request $request, WarrantyRequest $warranty)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Solo los administradores pueden procesar las garantías.');
        }

        if ($user->id !== 1) {
            $descendants = $user->getDescendantsIds();
            if (!in_array($warranty->client->user_id, $descendants)) {
                abort(403, 'No autorizado para editar esta garantía.');
            }
        }
        $request->validate([
            'status' => 'required|in:approved,rejected,resolved',
            'new_email' => 'nullable|email|required_if:status,approved|required_if:type,replacement',
            'admin_notes' => 'nullable|string',
        ]);

        $warranty->status = $request->status;
        $warranty->admin_notes = $request->admin_notes;

        if ($request->status === 'approved' && $warranty->type === 'replacement') {
            $warranty->new_email = $request->new_email;
            
            // Lógica de reemplazo
            $oldAllowedEmail = AllowedEmail::where('email', $warranty->old_email)->first();
            
            if ($oldAllowedEmail) {
                // Calcular tiempo perdido desde que se pausó
                $diasPausados = 0;
                if ($oldAllowedEmail->paused_at) {
                    $diasPausados = Carbon::parse($oldAllowedEmail->paused_at)->diffInDays(Carbon::now());
                }

                $newExpiresAt = $oldAllowedEmail->expires_at 
                    ? Carbon::parse($oldAllowedEmail->expires_at)->addDays($diasPausados) 
                    : null;

                // Desvincular correo viejo (eliminarlo o desactivarlo)
                $oldAllowedEmail->delete(); 
                
                // Crear correo nuevo
                $newAllowedEmail = AllowedEmail::create([
                    'email'            => $request->new_email,
                    'user_id'          => $warranty->client->user_id ?? null,
                    'description'      => $oldAllowedEmail->description ?? null,
                    'email_account_id' => $oldAllowedEmail->email_account_id ?? null,
                    'platform_id'      => $oldAllowedEmail->platform_id ?? null,
                    'is_active'        => true,
                    'is_public'        => $oldAllowedEmail->is_public ?? false,
                ]);

                // Asociar al cliente
                $newAllowedEmail->clients()->attach($warranty->client_id);
            }
        } elseif ($request->status === 'resolved' && $warranty->type === 'minor_issue') {
            // Reanudar tiempo sin cambiar correo
            $oldAllowedEmail = AllowedEmail::where('email', $warranty->old_email)->first();
            if ($oldAllowedEmail && $oldAllowedEmail->paused_at) {
                $diasPausados = Carbon::parse($oldAllowedEmail->paused_at)->diffInDays(Carbon::now());
                $newExpiresAt = $oldAllowedEmail->expires_at 
                    ? Carbon::parse($oldAllowedEmail->expires_at)->addDays($diasPausados) 
                    : null;

                $oldAllowedEmail->update([
                    'expires_at' => $newExpiresAt,
                    'paused_at' => null, // reanudar
                ]);
            }
        }

        $warranty->resolved_at = Carbon::now();
        $warranty->save();

        return redirect()->route('admin.warranties.index')->with('success', 'Garantía gestionada y tiempo reanudado correctamente.');
    }
}
