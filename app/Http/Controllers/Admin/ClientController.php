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
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('status'));
            });

        // Solo Super Admin (ID 1) puede ver a todos. Todos los demás ven solo su red.
        if (auth()->id() !== 1) {
            $query->whereIn('user_id', auth()->user()->getDescendantsIds());
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

        $allowedEmails = AllowedEmail::where('is_active', true)
            ->visibleToUser($user)
            ->orderBy('email')
            ->get();

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
            'expires_at' => 'nullable|array',
            'expires_at.*' => 'nullable|date',
            'platforms' => 'nullable|array',
            'platforms.*' => 'exists:platforms,id',
        ]);

        if ($user->id !== 1) {
            $rootFranchise = $user->getRootFranchise();
            $plan = $rootFranchise->franchisePlan;

            // Límites por defecto si la franquicia matriz no tiene plan asignado
            $maxClients        = $plan?->max_clients ?? 10;
            $maxQueriesPerDay  = $plan?->max_queries_per_day_per_client ?? 50;

            if ($maxClients !== null) {
                // Bolsa compartida: sumar los clientes del root y de todos sus descendientes
                $allIds = array_merge([$rootFranchise->id], $rootFranchise->getDescendantsIds());
                $currentClientsCount = Client::whereIn('user_id', $allIds)->count();

                if ($currentClientsCount >= $maxClients) {
                    return back()->withInput()->withErrors([
                        'error' => "La red de tu Franquicia ha alcanzado el límite máximo de clientes permitidos ({$maxClients}). Contacta al administrador para ampliar el plan."
                    ]);
                }
            }

            // Verificar límite de consultas por día
            $requestedQueries = $validated['max_queries_per_day'] ?? 100;
            if ($requestedQueries > $maxQueriesPerDay) {
                return back()->withInput()->withErrors([
                    'error' => "El plan de tu Franquicia solo permite un máximo de {$maxQueriesPerDay} consultas por cliente al día."
                ]);
            }

            // Asegurarse de que el valor solicitado no supere el límite del plan
            $validated['max_queries_per_day'] = min($requestedQueries, $maxQueriesPerDay);
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['max_queries_per_day'] = $validated['max_queries_per_day'] ?? 100;
        $validated['access_mode'] = $validated['access_mode'] ?? 'all';
        $validated['user_id'] = $user->id;

        $client = Client::create($validated);

        // Guardar permisos de correos con CRM (Fechas de Vencimiento)
        if ($client->access_mode === 'selective') {
            $syncData = [];
            if (!empty($validated['allowed_emails'])) {
                foreach ($validated['allowed_emails'] as $emailId) {
                    $expiresAt = isset($validated['expires_at'][$emailId]) ? $validated['expires_at'][$emailId] : null;
                    $syncData[$emailId] = [
                        'assigned_at' => now(),
                        'expires_at' => $expiresAt,
                    ];
                }
            }
            $client->allowedEmails()->sync($syncData);
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

        $allowedEmails = AllowedEmail::where('is_active', true)
            ->visibleToUser($user)
            ->orderBy('email')
            ->get();

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
            'expires_at' => 'nullable|array',
            'expires_at.*' => 'nullable|date',
            'platforms' => 'nullable|array',
            'platforms.*' => 'exists:platforms,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['max_queries_per_day'] = $validated['max_queries_per_day'] ?? 100;
        $validated['access_mode'] = $validated['access_mode'] ?? 'all';

        if ($user->id !== 1) {
            $rootFranchise = $user->getRootFranchise();
            $plan = $rootFranchise->franchisePlan;

            // Límites por defecto si la franquicia matriz no tiene plan asignado
            $maxQueriesPerDay = $plan?->max_queries_per_day_per_client ?? 50;

            if ($validated['max_queries_per_day'] > $maxQueriesPerDay) {
                return back()->withInput()->withErrors([
                    'error' => "El plan de tu Franquicia solo permite un máximo de {$maxQueriesPerDay} consultas por cliente al día."
                ]);
            }

            // Forzar que no supere el límite del plan
            $validated['max_queries_per_day'] = min($validated['max_queries_per_day'], $maxQueriesPerDay);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $client->update($validated);

        // Actualizar permisos de correos con CRM (Fechas de Vencimiento)
        if ($client->access_mode === 'selective') {
            $syncData = [];
            if (!empty($validated['allowed_emails'])) {
                foreach ($validated['allowed_emails'] as $emailId) {
                    $expiresAt = isset($validated['expires_at'][$emailId]) ? $validated['expires_at'][$emailId] : null;
                    $syncData[$emailId] = [
                        'assigned_at' => now(),
                        'expires_at' => $expiresAt,
                    ];
                }
            }
            $client->allowedEmails()->sync($syncData);
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
