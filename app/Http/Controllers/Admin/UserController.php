<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        $users = User::when($currentUser->id !== 1, function ($query) use ($currentUser) {
                return $query->where('parent_id', $currentUser->id);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = \App\Models\FranchisePlan::where('is_active', true)->get();
        return view('admin.users.create', compact('plans'));
    }

    /**
     * Crear usuario rápidamente desde el login de admin (rol 'user' automático)
     */
    public function createQuick(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'name' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => 'user', // Rol automático
            'is_active' => true,
        ]);

        return redirect()->route('login')
            ->with('success', 'Usuario "' . $user->username . '" creado exitosamente. Ahora puedes iniciar sesión.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:admin,user,client',
            'is_active' => 'boolean',
            'plan_id' => 'nullable|exists:franchise_plans,id',
            'subscription_ends_at' => 'nullable|date',
            'grace_days' => 'nullable|integer|min:0',
        ]);

        // Lógica de roles y planes según jerarquía
        if (auth()->id() !== 1) {
            // Admin y Staff forzosamente crean Revendedores sin plan propio (cascada)
            $validated['role'] = 'user';
            $validated['plan_id'] = null;
            unset($validated['subscription_ends_at']);
        } else {
            // Super Admin
            if (!isset($validated['role']) || empty($validated['role'])) {
                $validated['role'] = 'user';
            }
            if ($validated['role'] !== 'admin') {
                $validated['subscription_ends_at'] = null;
            }
        }

        // Asignar al creador actual
        $validated['parent_id'] = auth()->id();

        // Evitar error de base de datos si el nombre completo se deja en blanco
        $validated['name'] = $validated['name'] ?? $validated['username'];

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (auth()->id() !== 1 && $user->parent_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar a este usuario.');
        }

        if ($user->id === 1) {
            abort(403, 'No tienes permiso para editar al Super Admin.');
        }

        $plans = \App\Models\FranchisePlan::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (auth()->id() !== 1 && $user->parent_id !== auth()->id()) {
            abort(403, 'No tienes permiso para modificar a este usuario.');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|in:admin,user,client',
            'is_active' => 'boolean',
            'plan_id' => 'nullable|exists:franchise_plans,id',
            'subscription_ends_at' => 'nullable|date',
            'grace_days' => 'nullable|integer|min:0',
        ]);

        if (auth()->id() !== 1) {
            $validated['role'] = 'user';
            $validated['plan_id'] = null;
            unset($validated['subscription_ends_at']);
        } else {
            if (!isset($validated['role']) || empty($validated['role'])) {
                $validated['role'] = 'user';
            }
            if ($validated['role'] !== 'admin') {
                $validated['subscription_ends_at'] = null;
            }
        }

        // Evitar error de base de datos si el nombre completo se deja en blanco
        $validated['name'] = $validated['name'] ?? $validated['username'];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() !== 1 && $user->parent_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar a este usuario.');
        }

        // Prevenir auto-eliminación
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Prevenir eliminación si tiene clientes asignados (quedarían huérfanos)
        $clientCount = $user->clients()->count();
        if ($clientCount > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', "No puedes eliminar este franquiciado porque tiene {$clientCount} cliente(s) asignado(s). Reasígnalos o elimínalos primero.");
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Renew a franchise user for 30 days.
     */
    public function renew(User $user)
    {
        if (auth()->id() !== 1) {
            abort(403, 'Solo el Súper Administrador puede renovar franquicias.');
        }

        $now = now();
        $currentExpiration = $user->subscription_ends_at;

        if ($currentExpiration && $currentExpiration > $now) {
            // Add 30 days to existing future date
            $user->subscription_ends_at = $currentExpiration->addDays(30);
        } else {
            // Add 30 days from today
            $user->subscription_ends_at = $now->addDays(30);
        }

        $user->save();

        return redirect()->back()->with('success', 'La suscripción de ' . $user->username . ' ha sido renovada exitosamente por 30 días (+).');
    }
}
