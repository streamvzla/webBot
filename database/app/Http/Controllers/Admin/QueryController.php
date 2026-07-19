<?php

namespace App\Http\Controllers\Admin;

use App\Models\Query;
use App\Models\AllowedEmail;
use Illuminate\Http\Request;

class QueryController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Query::with(['user', 'client', 'platform'])
            ->when($request->search, function ($q, $search) {
                return $q->where('email', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            })
            ->when($request->platform_id, function ($q, $platformId) {
                return $q->where('platform_id', $platformId);
            })
            ->when($request->result, function ($q, $result) {
                return $q->where('result', $result);
            });

        // Si es user, solo ve queries de sus clientes (a través del user_id)
        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        $queries = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.queries.index', compact('queries'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Query $query)
    {
        return view('admin.queries.show', compact('query'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Query $query)
    {
        $user = auth()->user();

        // Si es user, solo puede eliminar queries de sus clientes
        if ($user->role === 'user') {
            if ($query->user_id !== $user->id) {
                abort(403, 'No tienes autorización para eliminar este registro.');
            }
        }

        $query->delete();

        return redirect()->route('admin.queries.index')
            ->with('success', 'Registro eliminado exitosamente.');
    }

    /**
     * Remove all resources from storage.
     */
    public function truncate(Request $request)
    {
        $user = auth()->user();

        // Solo admins pueden truncar todos los registros
        if ($user->role !== 'admin') {
            abort(403, 'No tienes autorización para realizar esta acción.');
        }

        Query::truncate();

        return redirect()->route('admin.queries.index')
            ->with('success', 'Todos los registros han sido eliminados.');
    }
}
