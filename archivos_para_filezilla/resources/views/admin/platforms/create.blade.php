@extends('admin.layouts.app')

@section('title', 'Nueva Plataforma - Panel de Administración')

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

textarea.ui-input { min-height: 80px; resize: vertical; }
.ui-input.ui-error { border-color:rgba(239,68,68,0.4)!important; }
</style>

<div class="max-w-4xl mx-auto space-y-6" style="padding-bottom: 3rem;">

    {{-- BACK + HEADER --}}
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
            <h1 class="ui-hero-title">Nueva Plataforma</h1>
            <p class="ui-hero-sub">Agrega una nueva plataforma de streaming al catálogo.</p>
        </div>
    </div>

    <form action="{{ route('admin.platforms.store') }}" method="POST" class="space-y-5" enctype="multipart/form-data">
        @csrf

        {{-- ══ BLOQUE 1: Información Básica ══ --}}
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
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
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
                    <input type="text" id="slug" name="slug" required value="{{ old('slug') }}"
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
                <textarea id="description" name="description" class="ui-input" placeholder="Breve nota sobre el uso de la plataforma...">{{ old('description') }}</textarea>
            </div>
            </div>
        </div>

        {{-- ══ BLOQUE 2: Apariencia y Marca ══ --}}
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
                        <svg style="width:2.5rem;height:2.5rem;margin:0 auto 0.5rem;color:rgba(168,85,247,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p style="font-size:0.875rem;color:rgba(148,163,184,0.7);margin-bottom:0.25rem;font-weight:600;">Subir imagen</p>
                        <p style="font-size:0.75rem;color:rgba(100,116,139,0.5);">PNG transparente recomendado</p>
                        <input type="file" id="logo" name="logo" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <img id="logo-preview" class="image-preview" alt="Vista previa">
                    </div>
                    @error('logo')<p class="ui-err">{{ $message }}</p>@enderror
                </div>

                {{-- Color --}}
                <div style="display:flex;flex-direction:column;justify-content:center;">
                    <label for="color" class="ui-label">Color Distintivo</label>
                    <div class="color-picker-wrap">
                        <input type="color" id="color" name="color" value="{{ old('color', '#a855f7') }}" class="color-input">
                        <div style="flex:1;">
                            <input type="text" id="color-hex" value="{{ old('color', '#a855f7') }}" class="ui-input" style="font-family:monospace;text-transform:uppercase;" oninput="document.getElementById('color').value=this.value">
                            <p class="ui-hint">Se usa para bordes y acentos visuales.</p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        {{-- ══ BLOQUE 3: Estado y Permisos ══ --}}
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
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="ui-toggle-inp">
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
                        <input type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }} class="ui-toggle-inp">
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

        {{-- ══ BOTONES ══ --}}
        <div class="ui-anim-in ui-delay-3 ui-form-actions" style="padding-bottom: 4rem;">
            <a href="{{ route('admin.platforms.index') }}" wire:navigate class="ui-btn ui-btn-secondary ui-btn-large" style="flex:1;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancelar
            </a>
            <button type="submit" class="ui-btn ui-btn-primary ui-btn-large" style="flex:2;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Crear Plataforma
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    // Preview de imagen
    function previewImage(input) {
        const preview = document.getElementById('logo-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('show');
                input.parentElement.style.padding = '1.5rem 2rem';
                input.parentElement.querySelector('svg').style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto-slug
    document.getElementById('name').addEventListener('input', function(e) {
        let slug = e.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        document.getElementById('slug').value = slug;
    });
</script>
@endpush
@endsection
