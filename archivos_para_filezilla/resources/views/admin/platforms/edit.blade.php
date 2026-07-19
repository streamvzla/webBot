@extends('admin.layouts.app')

@section('title', 'Editar Plataforma - Panel de Administración')

@section('content')
<style>
/* ════════════════════════════════════════════════════
   PLATFORMS FORM — GOD LEVEL DESIGN
   Paleta: #050510 | #7c3aed | #a855f7 | #ec4899
════════════════════════════════════════════════════ */

/* ERROR & HINT */
.ui-err { font-size:0.75rem;color:#f43f5e;margin-top:0.4rem;display:flex;align-items:center;gap:0.3rem; }
.ui-hint { font-size:0.72rem;color:rgba(148,163,184,0.5);margin-top:0.4rem; }

/* FILE UPLOAD */
.file-upload-area { border: 1.5px dashed rgba(168,85,247,0.25); border-radius: 0.875rem; padding: 2rem; text-align: center; background: rgba(168,85,247,0.03); cursor: pointer; transition: all 0.2s; }
.file-upload-area:hover { border-color: rgba(168,85,247,0.5); background: rgba(168,85,247,0.06); }
.image-preview { max-width: 120px; max-height: 120px; border-radius: 0.75rem; margin: 1rem auto 0; display: none; border: 1px solid rgba(168,85,247,0.2); }
.image-preview.show { display: block; }

/* COLOR PICKER */
.color-picker-wrap { display: flex; align-items: center; gap: 1rem; }
.color-input { width: 3rem; height: 3rem; padding: 0; border: none; border-radius: 0.75rem; cursor: pointer; background: none; }
.color-input::-webkit-color-swatch-wrapper { padding: 0; }
.color-input::-webkit-color-swatch { border: 2px solid rgba(255,255,255,0.1); border-radius: 0.75rem; box-shadow: 0 0 15px rgba(0,0,0,0.2); }

/* BUTTON ADD (Para subjects) */
.ui-btn-add { display:inline-flex;align-items:center;justify-content:center;gap:0.3rem;background:rgba(168,85,247,0.1);border:1px solid rgba(168,85,247,0.3);border-radius:0.625rem;color:#c4b5fd;font-weight:700;font-size:0.8rem;padding:0.6rem 1rem;cursor:pointer;transition:all 0.2s;white-space:nowrap;height:48px; }
.ui-btn-add:hover { background:rgba(168,85,247,0.2);color:white; }

/* SUBJECTS TABLE */
.subj-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.subj-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(168,85,247,0.8); border-bottom: 1px solid rgba(168,85,247,0.15); }
.subj-table td { padding: 0.875rem 1rem; font-size: 0.875rem; color: rgba(226,232,240,0.85); border-bottom: 1px solid rgba(255,255,255,0.04); }
.subj-table tr:hover td { background: rgba(168,85,247,0.02); }
.subj-row { display: flex; align-items: center; gap: 0.5rem; }
.subj-icon { flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: rgba(168,85,247,0.1); color: #c4b5fd; border: 1px solid rgba(168,85,247,0.2); }

textarea.ui-input { min-height: 80px; resize: vertical; }
.ui-input.ui-error { border-color:rgba(239,68,68,0.4)!important; }
</style>

<div class="max-w-4xl mx-auto space-y-6" style="padding-bottom: 3rem;">

    <div class="ui-anim-in" style="margin-bottom: 1rem;">
        <a href="{{ route('admin.platforms.index') }}" wire:navigate
           style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.8rem;font-weight:600;color:rgba(148,163,184,0.6);text-decoration:none;transition:color 0.2s;"
           onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.6)'">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Plataformas
        </a>
    </div>

    <div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
        <div>
            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#7c3aed;margin-bottom:0.5rem;">
                Administración Global
            </div>
            <h1 class="ui-hero-title">Editar Plataforma: {{ $platform->name }}</h1>
            <p class="ui-hero-sub">Modifica los detalles y configuraciones de esta plataforma.</p>
        </div>
    </div>

    <form id="edit-platform-form" action="{{ route('admin.platforms.update', $platform) }}" method="POST" class="space-y-5" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- BLOQUE 1: Información Básica --}}
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        </div>
                        Información Principal
                    </div>
                </div>
            </div>
            <div class="ae-card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                {{-- Nombre --}}
                <div>
                    <label for="name" class="ui-label">Nombre de la Plataforma *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $platform->name) }}"
                           class="ui-input {{ $errors->has('name') ? 'ui-error' : '' }}"
                           placeholder="Ej: Netflix, Spotify..." autofocus>
                    @error('name')
                        <p class="ui-err">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="ui-label">Slug (URL Amigable) *</label>
                    <input type="text" id="slug" name="slug" required value="{{ old('slug', $platform->slug) }}"
                           class="ui-input {{ $errors->has('slug') ? 'ui-error' : '' }}"
                           placeholder="netflix, spotify" style="font-family:monospace;color:#c4b5fd;">
                    @error('slug')
                        <p class="ui-err">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Descripción --}}
            <div>
                <label for="description" class="ui-label">Descripción</label>
                <textarea id="description" name="description" class="ui-input" placeholder="Breve nota sobre el uso de la plataforma...">{{ old('description', $platform->description) }}</textarea>
            </div>
            </div>
        </div>

        {{-- BLOQUE 2: Apariencia y Marca --}}
        <div class="ae-card ui-anim-in ui-delay-2">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        Apariencia y Marca
                    </div>
                </div>
            </div>
            <div class="ae-card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Logo --}}
                <div>
                    <label class="ui-label">Logo de Plataforma</label>
                    <div class="file-upload-area" onclick="document.getElementById('logo').click()">
                        @if($platform->logo)
                            <img id="logo-preview" class="image-preview show" src="{{ asset(str_starts_with($platform->logo, 'platforms_logos') ? $platform->logo : 'storage/' . $platform->logo) }}" alt="{{ $platform->name }}">
                            <p style="font-size:0.75rem;color:rgba(168,85,247,0.8);margin-top:0.75rem;font-weight:600;">Haz clic para cambiar logo</p>
                        @else
                            <svg id="logo-icon" style="width:2.5rem;height:2.5rem;margin:0 auto 0.5rem;color:rgba(168,85,247,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p id="logo-text" style="font-size:0.875rem;color:rgba(148,163,184,0.7);margin-bottom:0.25rem;font-weight:600;">Subir imagen</p>
                            <p id="logo-hint" style="font-size:0.75rem;color:rgba(100,116,139,0.5);">PNG transparente recomendado</p>
                            <img id="logo-preview" class="image-preview" alt="Vista previa">
                        @endif
                        <input type="file" id="logo" name="logo" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </div>
                    @error('logo')<p class="ui-err">{{ $message }}</p>@enderror
                </div>

                {{-- Color --}}
                <div style="display:flex;flex-direction:column;justify-content:center;">
                    <label for="color" class="ui-label">Color Distintivo</label>
                    <div class="color-picker-wrap">
                        <input type="color" id="color" name="color" value="{{ old('color', $platform->color ?? '#a855f7') }}" class="color-input">
                        <div style="flex:1;">
                            <input type="text" id="color-hex" value="{{ old('color', $platform->color ?? '#a855f7') }}" class="ui-input" style="font-family:monospace;text-transform:uppercase;" oninput="document.getElementById('color').value=this.value">
                            <p class="ui-hint">Se usa para bordes y acentos visuales.</p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        {{-- BLOQUE 3: Estado y Permisos --}}
        <div class="ae-card ui-anim-in ui-delay-3">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        Estado y Permisos
                    </div>
                </div>
            </div>
            <div class="ae-card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Toggle Activo --}}
                <div style="padding:1.5rem;border-radius:0.875rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;gap:1.25rem;">
                    <label class="ui-toggle-wrap">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $platform->is_active) ? 'checked' : '' }} class="ui-toggle-inp">
                        <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                    </label>
                    <div>
                        <label for="is_active" style="font-size:1rem;font-weight:700;color:white;cursor:pointer;">Plataforma Activa</label>
                        <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);margin-top:0.2rem;">Si se apaga, no se podrán crear cuentas asociadas a esta.</p>
                    </div>
                </div>

                {{-- Toggle Público --}}
                <div style="padding:1.5rem;border-radius:0.875rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;gap:1.25rem;">
                    <label class="ui-toggle-wrap">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public', $platform->is_public) ? 'checked' : '' }} class="ui-toggle-inp">
                        <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                    </label>
                    <div>
                        <label for="is_public" style="font-size:1rem;font-weight:700;color:white;cursor:pointer;display:flex;align-items:center;gap:0.3rem;">
                            <svg width="14" height="14" fill="none" stroke="#38bdf8" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Acceso Público
                        </label>
                        <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);margin-top:0.2rem;">Visible para clientes en la web principal.</p>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </form>

    {{-- ══ BLOQUE 4: Reglas de Búsqueda ══ --}}
    <div class="ae-card ui-anim-in ui-delay-4" style="margin-top: 2rem;">
        <div class="ae-card-head">
            <div class="ae-card-title">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ui-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    Reglas de Búsqueda (Asuntos de Email)
                </div>
            </div>
        </div>
        <div class="ae-card-body">

        <p style="font-size:0.875rem;color:rgba(148,163,184,0.7);margin: 0 0 1.5rem;">
            Añade los asuntos exactos que el sistema buscará en la bandeja de entrada para atrapar los códigos de <strong>{{ $platform->name }}</strong>. Usa <code>[email]</code> como comodín.
        </p>

        <form action="{{ route('admin.platforms.subjects.store', $platform) }}" method="POST" style="display:flex;gap:1rem;align-items:flex-start;">
            @csrf
            <div style="flex:1;">
                <input type="text" name="subject" required
                       class="ui-input {{ $errors->has('subject') ? 'ui-error' : '' }}"
                       placeholder="Ej: Tu código de Netflix es [email]">
                @error('subject')<p class="ui-err">{{ $message }}</p>@enderror
                <div style="margin-top:0.75rem;display:flex;align-items:center;gap:0.75rem;">
                    <label class="ui-toggle-wrap" style="transform:scale(0.85); transform-origin:left center; margin:0;">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" name="is_public" id="is_public_new" value="1" class="ui-toggle-inp">
                        <div class="ui-toggle-track" style="margin:0;"><div class="ui-toggle-thumb"></div></div>
                    </label>
                    <label for="is_public_new" style="font-size:0.8rem;color:rgba(255,255,255,0.7);cursor:pointer;">Mostrar en Query Público</label>
                </div>
            </div>
            <button type="submit" class="ui-btn-add">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Agregar
            </button>
        </form>

        @if($platform->subjects->count() > 0)
            <div style="margin-top:1.5rem;">
                <table class="subj-table">
                    <thead>
                        <tr>
                            <th>Asunto Configurado</th>
                            <th style="text-align:center;">Público</th>
                            <th style="text-align:right;">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($platform->subjects as $subject)
                            <tr>
                                <td>
                                    <div class="subj-row">
                                        <div class="subj-icon">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <span style="font-weight:600;">{!! str_replace('[email]', '<span style="color:#a855f7;background:rgba(168,85,247,0.1);padding:0.1rem 0.4rem;border-radius:0.3rem;">[email]</span>', e($subject->subject)) !!}</span>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <label class="ui-toggle-wrap" style="display:inline-block; margin:auto; transform:scale(0.85);">
                                        <input type="checkbox" class="ui-toggle-inp toggle-public-subject" data-url="{{ route('admin.platforms.subjects.togglePublic', [$platform, $subject]) }}" {{ $subject->is_public ? 'checked' : '' }}>
                                        <div class="ui-toggle-track" style="margin:0;"><div class="ui-toggle-thumb"></div></div>
                                    </label>
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('admin.platforms.subjects.destroy', [$platform, $subject]) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#f87171;width:2rem;height:2rem;border-radius:0.5rem;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.2)';this.style.color='white'" onmouseout="this.style.background='rgba(239,68,68,0.1)';this.style.color='#f87171'">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="margin-top:1.5rem;text-align:center;padding:2rem;border:1px dashed rgba(255,255,255,0.1);border-radius:1rem;">
                <p style="font-size:0.875rem;color:rgba(148,163,184,0.6);">No hay asuntos configurados. El sistema no podrá atrapar códigos para esta plataforma.</p>
            </div>
        @endif
        </div>
    </div>

    {{-- BOTONES (MOVIDOS AL FINAL) --}}
    <div class="ui-anim-in ui-delay-5 ui-form-actions" style="margin-top:2rem; padding-bottom:4rem;">
        <a href="{{ route('admin.platforms.index') }}" wire:navigate class="ui-btn ui-btn-secondary ui-btn-large" style="flex:1;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Cancelar
        </a>
        <button type="submit" form="edit-platform-form" class="ui-btn ui-btn-primary ui-btn-large" style="flex:2;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            Guardar Cambios
        </button>
    </div>

</div>

@push('scripts')
<script>
    // Preview de imagen
    function previewImage(input) {
        const preview = document.getElementById('logo-preview');
        const icon = document.getElementById('logo-icon');
        const text = document.getElementById('logo-text');
        const hint = document.getElementById('logo-hint');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('show');
                if(icon) icon.style.display = 'none';
                if(text) text.style.display = 'none';
                if(hint) hint.style.display = 'none';
                input.parentElement.style.padding = '1.5rem 2rem';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto-slug
    document.getElementById('name').addEventListener('input', function(e) {
        let slug = e.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        document.getElementById('slug').value = slug;
    });

    // AJAX Toggle Public Subject
    document.querySelectorAll('.toggle-public-subject').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const url = this.getAttribute('data-url');
            const isChecked = this.checked;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    alert('Error al actualizar');
                    this.checked = !isChecked;
                }
            })
            .catch(err => {
                alert('Error de conexión');
                this.checked = !isChecked;
            });
        });
    });
</script>
@endpush
@endsection
