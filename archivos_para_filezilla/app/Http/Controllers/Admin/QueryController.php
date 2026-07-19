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
                return $q->where(function ($inner) use ($search) {
                    $inner->where('email', 'like', "%{$search}%")
                          ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->when($request->platform_id, function ($q, $platformId) {
                return $q->where('platform_id', $platformId);
            })
            ->when($request->result, function ($q, $result) {
                return $q->where('result', $result);
            });

        // Si no es Super Admin, solo ve queries de su red (él mismo o sus clientes)
        if ($user->id !== 1) {
            $query->whereIn('user_id', $user->getDescendantsIds());
        }

        $queries = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.queries.index', compact('queries'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Query $query)
    {
        $user = auth()->user();
        if ($user->id !== 1 && !in_array($query->user_id, $user->getDescendantsIds())) {
            abort(403, 'No autorizado.');
        }
        return view('admin.queries.show', compact('query'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Query $query)
    {
        $user = auth()->user();

        // Si no es super admin, solo puede eliminar queries de su red
        if ($user->id !== 1) {
            if (!in_array($query->user_id, $user->getDescendantsIds())) {
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
