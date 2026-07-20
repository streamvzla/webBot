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

        // En multi-tenancy, los usuarios normales ven solo sus propias plataformas, Super Admin ve todo
        if ($user->id !== 1) {
            $query->where('user_id', $user->id);
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
                        ->where('user_id', $user->id);
                    if ($platformId) {
                        $query->where('id', '!=', $platformId);
                    }
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
                        ->where('user_id', $user->id);
                    if ($platformId) {
                        $query->where('id', '!=', $platformId);
                    }
                    if ($query->exists()) {
                        $fail('Ya existe una plataforma con este slug en tu cuenta.');
                    }
                },
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_public'] = $request->boolean('is_public', false);
        $validated['user_id'] = $user->id; // Asignar al usuario actual

        // Handle logo upload (Directo a public/platforms_logos para evitar problemas de symlink en Cpanel)
        $logoPath = null;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $file = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('platforms_logos'));
            $file->move(public_path('platforms_logos'), $filename);
            $logoPath = 'platforms_logos/' . $filename;
        }
        $validated['logo'] = $logoPath;
        
        // En multi-tenancy estricto, la plataforma siempre le pertenece a quien la crea
        $validated['user_id'] = $user->id;

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

        // Verificar permisos: solo puede editar si le pertenece
        if (\$user->id === 1 || \$platform->user_id === \$user->id) {
            return view('admin.platforms.edit', compact('platform'));
        }

        abort(403, 'No tienes autorizaciÃ³n para editar esta plataforma.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Platform $platform)
    {
        $user = auth()->user();

        // Verificar permisos: solo puede editar si le pertenece
        if (\$user->id !== 1 && \$platform->user_id !== \$user->id) {
            abort(403, 'No tienes autorizaciÃ³n para editar esta plataforma.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($user, $platform) {
                    $query = Platform::where('name', $value)
                        ->where('id', '!=', $platform->id)
                        ->where('user_id', $user->id);
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
                        ->where('user_id', $user->id);
                    if ($query->exists()) {
                        $fail('Ya existe una plataforma con este slug en tu cuenta.');
                    }
                },
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_public'] = $request->boolean('is_public', false);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists (verificar si estaba en public o en storage)
            if ($platform->logo && file_exists(public_path($platform->logo))) {
                @unlink(public_path($platform->logo));
            } elseif ($platform->logo && Storage::disk('public')->exists($platform->logo)) {
                Storage::disk('public')->delete($platform->logo);
            }
            
            $file = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('platforms_logos'));
            $file->move(public_path('platforms_logos'), $filename);
            $validated['logo'] = 'platforms_logos/' . $filename;
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

        // Verificar permisos: solo puede eliminar si le pertenece
        if (\$user->id !== 1 && \$platform->user_id !== \$user->id) {
            abort(403, 'No tienes autorizaciÃ³n para eliminar esta plataforma.');
        }

        // Delete logo if exists
        if ($platform->logo && file_exists(public_path($platform->logo))) {
            @unlink(public_path($platform->logo));
        } elseif ($platform->logo && Storage::disk('public')->exists($platform->logo)) {
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

        // Verificar permisos: solo puede ver si le pertenece
        if (\$user->id === 1 || \$platform->user_id === \$user->id) {
            $subjects = $platform->subjects()->orderBy('subject')->get();
            return view('admin.platforms.subjects', compact('platform', 'subjects'));
        }

        abort(403, 'No tienes autorizaciÃ³n para ver esta plataforma.');
    }

    /**
     * Store a new subject for a platform.
     */
    public function storeSubject(Request $request, Platform $platform)
    {
        $user = auth()->user();

        // Verificar permisos: solo puede modificar si le pertenece
        if (\$user->id !== 1 && \$platform->user_id !== \$user->id) {
            abort(403, 'No tienes autorizaciÃ³n para modificar esta plataforma.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255|unique:platform_subjects,subject,NULL,id,platform_id,' . $platform->id,
            'pattern' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ]);

        $validated['is_public'] = $request->boolean('is_public', false);

        $platform->subjects()->create($validated);

        return back()->with('success', 'Asunto agregado exitosamente.');
    }

    /**
     * Destroy a subject.
     */
    public function destroySubject(Platform $platform, PlatformSubject $subject)
    {
        $user = auth()->user();

        // Verificar permisos: solo puede modificar si le pertenece
        if (\$user->id !== 1 && \$platform->user_id !== \$user->id) {
            abort(403, 'No tienes autorizaciÃ³n para modificar esta plataforma.');
        }

        $subject->delete();

        return back()->with('success', 'Asunto eliminado exitosamente.');
    }

    /**
     * Toggle visibility of a subject (AJAX).
     */
    public function toggleSubjectVisibility(Request $request, Platform $platform, PlatformSubject $subject)
    {
        $user = auth()->user();

        // Verificar permisos
        if (\$user->id !== 1 && \$platform->user_id !== \$user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $subject->is_public = !$subject->is_public;
        $subject->save();

        return response()->json([
            'success' => true,
            'is_public' => $subject->is_public
        ]);
    }
}

