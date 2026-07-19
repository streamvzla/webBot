<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllowedEmail;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Http\Request;

class AllowedEmailController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = AllowedEmail::with('platform')
            ->with('user')
            ->when($request->search, function ($query, $search) {
                return $query->where('email', 'like', "%{$search}%");
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('status'));
            })
            ->when($request->public !== null && $request->public !== '', function ($query) use ($request) {
                return $query->where('is_public', $request->boolean('public'));
            })
            ->when($request->platform_id, function ($query, $platformId) {
                return $query->where('platform_id', $platformId);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            });

        // Los administradores ven todos los correos
        // Los usuarios normales solo ven sus propios correos
        if ($user && $user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        $allowedEmails = $query->orderBy('email')->paginate(20);

        // Obtener usuarios para el filtro (solo para admins)
        $users = [];
        if ($user->role === 'admin') {
            $users = User::orderBy('name')->get();
        }

        // Obtener plataformas para el filtro
        $platformsFilter = Platform::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.allowed-emails.index', compact('allowedEmails', 'users', 'platformsFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        // Obtener plataformas disponibles para el usuario
        $platforms = Platform::where('is_active', true)
            ->when($user->role === 'user', function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', $user->id);
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.allowed-emails.create', compact('platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $platformId = $request->input('platform_id');

                    $query = AllowedEmail::where('email', $value)
                        ->where(function ($q) use ($platformId) {
                            // Verificar tanto si platform_id es NULL como si coincide
                            if ($platformId === null || $platformId === '') {
                                $q->whereNull('platform_id');
                            } else {
                                $q->where('platform_id', $platformId);
                            }
                        });

                    if ($user->role === 'user') {
                        $query->where('user_id', $user->id);
                    }

                    if ($query->exists()) {
                        $fail('Ya existe un correo autorizado con este email para la plataforma seleccionada.');
                    }
                },
            ],
            'description' => 'nullable|string|max:255',
            'platform_id' => 'nullable|exists:platforms,id',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_public'] = $request->boolean('is_public', false);
        $validated['user_id'] = $user->id;

        AllowedEmail::create($validated);

        return redirect()->route('admin.allowed-emails.index')
            ->with('success', 'Correo autorizado creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AllowedEmail $allowedEmail)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $allowedEmail->user_id !== $user->id && !$allowedEmail->is_public) {
            abort(403, 'No tienes autorización para editar este correo.');
        }

        // Obtener plataformas disponibles para el usuario
        $platforms = Platform::where('is_active', true)
            ->when($user->role === 'user', function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->whereNull('user_id')
                      ->orWhere('user_id', $user->id);
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.allowed-emails.edit', compact('allowedEmail', 'platforms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AllowedEmail $allowedEmail)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $allowedEmail->user_id !== $user->id && !$allowedEmail->is_public) {
            abort(403, 'No tienes autorización para editar este correo.');
        }

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user, $allowedEmail) {
                    $platformId = $request->input('platform_id');

                    $query = AllowedEmail::where('email', $value)
                        ->where('id', '!=', $allowedEmail->id)
                        ->where(function ($q) use ($platformId) {
                            // Verificar tanto si platform_id es NULL como si coincide
                            if ($platformId === null || $platformId === '') {
                                $q->whereNull('platform_id');
                            } else {
                                $q->where('platform_id', $platformId);
                            }
                        });

                    if ($user->role === 'user') {
                        $query->where('user_id', $user->id);
                    }

                    if ($query->exists()) {
                        $fail('Ya existe un correo autorizado con este email para la plataforma seleccionada.');
                    }
                },
            ],
            'description' => 'nullable|string|max:255',
            'platform_id' => 'nullable|exists:platforms,id',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_public'] = $request->boolean('is_public', false);

        $allowedEmail->update($validated);

        return redirect()->route('admin.allowed-emails.index')
            ->with('success', 'Correo autorizado actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AllowedEmail $allowedEmail)
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role === 'user' && $allowedEmail->user_id !== $user->id) {
            abort(403, 'No tienes autorización para eliminar este correo.');
        }

        $allowedEmail->delete();

        return redirect()->route('admin.allowed-emails.index')
            ->with('success', 'Correo autorizado eliminado exitosamente.');
    }
}
