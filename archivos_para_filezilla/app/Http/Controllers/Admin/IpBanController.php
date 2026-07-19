<?php

namespace App\Http\Controllers\Admin;

use App\Models\IpBan;
use Illuminate\Http\Request;

class IpBanController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = IpBan::with('client')
            ->orderBy('created_at', 'desc');

        // Los administradores ven todos los bans, los usuarios normales solo ven los de sus clientes
        if ($user->role === 'user') {
            $query->whereHas('client', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $bans = $query->paginate(20);

        return view('admin.ip-bans.index', compact('bans'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IpBan $ipBan)
    {
        $user = auth()->user();

        if ($user->role === 'user') {
            // Verificar que el ban pertenezca a un cliente de este usuario
            if (!$ipBan->client || $ipBan->client->user_id !== $user->id) {
                abort(403, 'No tienes autorización para desbanear esta IP.');
            }
        }

        $ipBan->delete();

        return back()->with('success', 'IP desbaneada exitosamente.');
    }
}
