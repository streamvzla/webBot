<?php

namespace App\Http\Controllers\Admin;

use App\Models\Platform;
use App\Models\PlatformSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Platform::withCount('subjects')
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('status'));
            });

        // Si es admin, ve las plataformas globales (user_id null) y las suyas propias
        // Si es user, solo ve sus propias plataformas
        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'admin') {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', $user->id);
            });
        }

        $platforms = $query->orderBy('name')->paginate(20);

        return view('admin.platforms.index', compact('platforms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.platforms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $platformId = $request->input('id'); // Para update
                    $query = Platform::where('name', $value)
                        ->where(function ($q) use ($user, $platformId) {
                            // Verificar plataformas propias del usuario
                            $q->where('user_id', $user->id);
                            // Para admins, también verificar plataformas globales
                            if ($user->role === 'admin') {
                                $q->orWhereNull('user_id');
                            }
                            // Excluir la plataforma actual si es update
                            if ($platformId) {
                                $q->where('id', '!=', $platformId);
                            }
                        });
                    if ($query->exists()) {
                        $fail('Ya existe una plataforma con este nombre en tu cuenta.');
                    }
                },
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $platformId = $request->input('id'); // Para update
                    $query = Platform::where('slug', $value)
                        ->where(function ($q) use ($user, $platformId) {
                            // Verificar plataformas propias del usuario
                            $q->where('user_id', $user->id);
                            // Para admins, también verificar plataformas globales
                            if ($user->role === 'admin') {
                                $q->orWhereNull('user_id');
                            }
                            // Excluir la plataforma actual si es update
                            if ($platformId) {
                                $q->where('id', '!=', $platformId);
                            }
                        });
                    if ($query->exists()) {
                        $fail('Ya existe una plataforma con este slug en tu cuenta.');
                    }
                },
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_public'] = $request->boolean('is_public', false);
        $validated['user_id'] = $user->id; // Asignar al usuario actual

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('platforms', 'public');
        }
        $validated['logo'] = $logoPath;

        Platform::create($validated);

        return redirect()->route('admin.platforms.index')
            ->with('success', 'Plataforma creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Platform $platform)
    {
        $user = auth()->user();

        // Verificar permisos: puede editar si es global (user_id null) o si le pertenece
        if (!$platform->user_id || $platform->user_id === $user->id) {
            return view('admin.platforms.edit', compact('platform'));
        }

        abort(403, 'No tienes autorización para editar esta plataforma.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Platform $platform)
    {
        $user = auth()->user();

        // Verificar permisos: puede editar si es global (user_id null) o si le pertenece
        if ($platform->user_id && $platform->user_id !== $user->id) {
            abort(403, 'No tienes autorización para editar esta plataforma.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($user, $platform) {
                    $query = Platform::where('name', $value)
                        ->where('id', '!=', $platform->id)
                        ->where(function ($q) use ($user) {
                            // Verificar plataformas propias del usuario
                            $q->where('user_id', $user->id);
                            // Para admins, también verificar plataformas globales
                            if ($user->role === 'admin') {
                                $q->orWhereNull('user_id');
                            }
                        });
                    if ($query->exists()) {
                        $fail('Ya existe una plataforma con este nombre en tu cuenta.');
                    }
                },
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($user, $platform) {
                    $query = Platform::where('slug', $value)
                        ->where('id', '!=', $platform->id)
                        ->where(function ($q) use ($user) {
                            // Verificar plataformas propias del usuario
                            $q->where('user_id', $user->id);
                            // Para admins, también verificar plataformas globales
                            if ($user->role === 'admin') {
                                $q->orWhereNull('user_id');
                            }
                        });
                    if ($query->exists()) {
                        $fail('Ya existe una plataforma con este slug en tu cuenta.');
                    }
                },
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_public'] = $request->boolean('is_public', false);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($platform->logo && Storage::disk('public')->exists($platform->logo)) {
                Storage::disk('public')->delete($platform->logo);
            }
            $validated['logo'] = $request->file('logo')->store('platforms', 'public');
        } else {
            $validated['logo'] = $platform->logo;
        }

        $platform->update($validated);

        return redirect()->route('admin.platforms.index')
            ->with('success', 'Plataforma actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Platform $platform)
    {
        $user = auth()->user();

        // Verificar permisos: puede eliminar si es global (user_id null) o si le pertenece
        if ($platform->user_id && $platform->user_id !== $user->id) {
            abort(403, 'No tienes autorización para eliminar esta plataforma.');
        }

        // Delete logo if exists
        if ($platform->logo && Storage::disk('public')->exists($platform->logo)) {
            Storage::disk('public')->delete($platform->logo);
        }

        $platform->delete();

        return redirect()->route('admin.platforms.index')
            ->with('success', 'Plataforma eliminada exitosamente.');
    }

    /**
     * Display subjects for a platform.
     */
    public function subjects(Platform $platform)
    {
        $user = auth()->user();

        // Verificar permisos: puede ver si es global (user_id null) o si le pertenece
        if (!$platform->user_id || $platform->user_id === $user->id) {
            $subjects = $platform->subjects()->orderBy('subject')->get();
            return view('admin.platforms.subjects', compact('platform', 'subjects'));
        }

        abort(403, 'No tienes autorización para ver esta plataforma.');
    }

    /**
     * Store a new subject for a platform.
     */
    public function storeSubject(Request $request, Platform $platform)
    {
        $user = auth()->user();

        // Verificar permisos: puede modificar si es global (user_id null) o si le pertenece
        if ($platform->user_id && $platform->user_id !== $user->id) {
            abort(403, 'No tienes autorización para modificar esta plataforma.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255|unique:platform_subjects,subject,NULL,id,platform_id,' . $platform->id,
        ]);

        $platform->subjects()->create($validated);

        return back()->with('success', 'Asunto agregado exitosamente.');
    }

    /**
     * Destroy a subject.
     */
    public function destroySubject(Platform $platform, PlatformSubject $subject)
    {
        $user = auth()->user();

        // Verificar permisos: puede modificar si es global (user_id null) o si le pertenece
        if ($platform->user_id && $platform->user_id !== $user->id) {
            abort(403, 'No tienes autorización para modificar esta plataforma.');
        }

        $subject->delete();

        return back()->with('success', 'Asunto eliminado exitosamente.');
    }
}
