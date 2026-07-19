<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllowedEmail;
use App\Models\Client;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Client::with('emailAccount')
            ->with('user')
            ->with('platforms')
            ->withCount('allowedEmails')
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('status'));
            });

        // Los usuarios con rol 'user' solo ven sus propios clientes
        if ($user && $user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        $clients = $query->orderBy('name')->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Los usuarios con rol 'user' solo pueden ver sus propios correos autorizados
        if ($user && $user->role === 'user') {
            $allowedEmails = AllowedEmail::where('is_active', true)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('is_public', true);
                })
                ->orderBy('email')
                ->get();
        } else {
            $allowedEmails = AllowedEmail::where('is_active', true)
                ->orderBy('email')
                ->get();
        }

        // Obtener plataformas disponibles
        $platforms = Platform::where('is_active', true)
            ->when($user->role === 'user', function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', $user->id);
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.clients.create', compact('allowedEmails', 'platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'password' => 'required|string|min:8',
            'is_active' => 'boolean',
            'email_account_id' => 'nullable|exists:email_accounts,id',
            'max_queries_per_day' => 'nullable|integer|min:1',
            'access_mode' => 'nullable|in:all,selective',
            'allowed_emails' => 'nullable|array',
            'allowed_emails.*' => 'exists:allowed_emails,id',
            'platforms' => 'nullable|array',
            'platforms.*' => 'exists:platforms,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['max_queries_per_day'] = $validated['max_queries_per_day'] ?? 100;
        $validated['access_mode'] = $validated['access_mode'] ?? 'all';
        $validated['user_id'] = $user->id;

        $client = Client::create($validated);

        // Guardar permisos de correos
        if ($client->access_mode === 'selective' && isset($validated['allowed_emails'])) {
            $client->allowedEmails()->sync($validated['allowed_emails']);
        }

        // Guardar plataformas asignadas
        if (isset($validated['platforms'])) {
            $client->platforms()->sync($validated['platforms']);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $client->user_id !== $user->id) {
            abort(403, 'No tienes autorización para editar este cliente.');
        }

        // Los usuarios con rol 'user' solo pueden ver sus propios correos autorizados
        if ($user && $user->role === 'user') {
            $allowedEmails = AllowedEmail::where('is_active', true)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('is_public', true);
                })
                ->orderBy('email')
                ->get();
        } else {
            $allowedEmails = AllowedEmail::where('is_active', true)
                ->orderBy('email')
                ->get();
        }

        // Obtener plataformas disponibles
        $platforms = Platform::where('is_active', true)
            ->when($user->role === 'user', function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', $user->id);
                });
            })
            ->orderBy('name')
            ->get();

        $client->load('allowedEmails');
        $client->load('platforms');

        return view('admin.clients.edit', compact('client', 'allowedEmails', 'platforms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $client->user_id !== $user->id) {
            abort(403, 'No tienes autorización para editar este cliente.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('clients')->ignore($client->id)],
            'password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
            'email_account_id' => 'nullable|exists:email_accounts,id',
            'max_queries_per_day' => 'nullable|integer|min:1',
            'access_mode' => 'nullable|in:all,selective',
            'allowed_emails' => 'nullable|array',
            'allowed_emails.*' => 'exists:allowed_emails,id',
            'platforms' => 'nullable|array',
            'platforms.*' => 'exists:platforms,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['max_queries_per_day'] = $validated['max_queries_per_day'] ?? 100;
        $validated['access_mode'] = $validated['access_mode'] ?? 'all';

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $client->update($validated);

        // Actualizar permisos de correos
        if ($client->access_mode === 'selective') {
            $client->allowedEmails()->sync($validated['allowed_emails'] ?? []);
        } else {
            $client->allowedEmails()->detach();
        }

        // Actualizar plataformas asignadas
        $client->platforms()->sync($validated['platforms'] ?? []);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $client->user_id !== $user->id) {
            abort(403, 'No tienes autorización para eliminar este cliente.');
        }

        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Reset daily query count for a client.
     */
    public function resetQueryCount(Client $client)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $client->user_id !== $user->id) {
            abort(403, 'No tienes autorización para modificar este cliente.');
        }

        $client->resetDailyQueryCount();

        return back()->with('success', 'Contador de consultas reiniciado.');
    }

    /**
     * Activate a client.
     */
    public function activate(Client $client)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $client->user_id !== $user->id) {
            abort(403, 'No tienes autorización para modificar este cliente.');
        }

        $client->update(['is_active' => true]);

        return back()->with('success', 'Cliente activado exitosamente.');
    }

    /**
     * Deactivate a client.
     */
    public function deactivate(Client $client)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $client->user_id !== $user->id) {
            abort(403, 'No tienes autorización para modificar este cliente.');
        }

        $client->update(['is_active' => false]);

        return back()->with('success', 'Cliente desactivado exitosamente.');
    }
}
