@extends('admin.layouts.app')

@section('title', 'Reglas de Búsqueda - ' . $platform->name)

@section('header', 'Reglas de Búsqueda')
@section('description', 'Gestiona los asuntos y patrones para ' . $platform->name)

@section('content')
<style>
    .form-card {
        background: linear-gradient(135deg, rgba(15,10,40,0.88) 0%, rgba(10,5,25,0.92) 100%);
        border: 1px solid rgba(168,85,247,0.15);
        border-radius: 1.5rem; padding: 2.5rem;
        position: relative; overflow: hidden; margin-bottom: 2rem;
    }
    .form-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, #a855f7, #ec4899, transparent);
    }
    .form-label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(148,163,184,0.7); margin-bottom: 0.5rem; }
    .form-input {
        width: 100%; padding: 0.75rem 1rem; background: rgba(255,255,255,0.04);
        border: 1.5px solid rgba(168,85,247,0.15); border-radius: 0.75rem;
        color: white; font-size: 0.9rem; outline: none; transition: all 0.2s; font-family: inherit;
    }
    .form-input:focus { border-color: rgba(168,85,247,0.5); background: rgba(168,85,247,0.05); box-shadow: 0 0 0 3px rgba(168,85,247,0.1); }
    .btn-add { display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; background: rgba(168,85,247,0.1); border: 1px solid rgba(168,85,247,0.3); border-radius: 0.625rem; color: #c4b5fd; font-weight: 700; font-size: 0.8rem; padding: 0.6rem 1rem; cursor: pointer; transition: all 0.2s; white-space: nowrap; width: 100%; height: 48px; }
    .btn-add:hover { background: rgba(168,85,247,0.2); color: white; }
    .subj-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .subj-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(168,85,247,0.8); border-bottom: 1px solid rgba(168,85,247,0.15); }
    .subj-table td { padding: 0.875rem 1rem; font-size: 0.875rem; color: rgba(226,232,240,0.85); border-bottom: 1px solid rgba(255,255,255,0.04); }
    .subj-table tr:hover td { background: rgba(168,85,247,0.02); }
    .subj-row { display: flex; align-items: center; gap: 0.5rem; }
    .subj-icon { flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; background: rgba(168,85,247,0.1); color: #c4b5fd; border: 1px solid rgba(168,85,247,0.2); }
</style>

<div class="max-w-4xl mx-auto space-y-6 animate-fade-in-up">
    
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('admin.platforms.index') }}" wire:navigate class="text-violet-400 hover:text-white transition text-sm font-semibold flex items-center gap-2">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a plataformas
        </a>
    </div>

    <div class="form-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;border-bottom:1px solid rgba(168,85,247,0.2);padding-bottom:1rem;">
            <div>
                <h3 style="font-size:1.25rem;font-weight:800;color:white;display:flex;align-items:center;gap:0.5rem;">
                    @if($platform->logo)
                        <img src="{{ asset(str_starts_with($platform->logo, 'platforms_logos') ? $platform->logo : 'storage/' . $platform->logo) }}" alt="{{ $platform->name }}" style="width:2rem;height:2rem;border-radius:0.5rem;object-fit:contain;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                    @endif
                    Reglas de Búsqueda para {{ $platform->name }}
                </h3>
                <p style="font-size:0.875rem;color:rgba(148,163,184,0.7);margin-top:0.25rem;">Configura qué asuntos de email identificarán los códigos de esta plataforma.</p>
            </div>
            <a href="{{ route('admin.platforms.edit', $platform) }}" wire:navigate style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);padding:0.5rem 1rem;border-radius:0.625rem;color:white;font-size:0.8rem;font-weight:700;">Editar Plataforma</a>
        </div>

        {{-- Formulario para Agregar Nuevo Subject --}}
        <form action="{{ route('admin.platforms.subjects.store', $platform) }}" method="POST" style="background:rgba(255,255,255,0.02);border:1px dashed rgba(168,85,247,0.3);border-radius:1rem;padding:1.5rem;margin-bottom:2rem;">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-5">
                    <label for="subject" class="form-label">Asunto / Remitente (Keyword) *</label>
                    <input type="text" id="subject" name="subject" required class="form-input" placeholder="Ej: Tu código de acceso a Netflix es...">
                </div>
                <div class="md:col-span-5">
                    <label for="pattern" class="form-label">Patrón Regex (Opcional)</label>
                    <input type="text" id="pattern" name="pattern" class="form-input" style="font-family:monospace;color:#fcd34d;" placeholder="/código:\s*([A-Z0-9]+)/i">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="btn-add">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Agregar
                    </button>
                </div>
            </div>
            @error('subject')<p style="font-size: 0.78rem; color: #f87171; margin-top: 0.35rem;">{{ $message }}</p>@enderror
            @error('pattern')<p style="font-size: 0.78rem; color: #f87171; margin-top: 0.35rem;">{{ $message }}</p>@enderror
        </form>

        {{-- Tabla de Subjects Existentes --}}
        @if($platform->subjects->count() > 0)
            <div style="overflow-x:auto;">
                <table class="subj-table">
                    <thead>
                        <tr>
                            <th>Asunto / Keyword</th>
                            <th>Regex / Extracción</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($platform->subjects as $subject)
                        <tr>
                            <td>
                                <div class="subj-row">
                                    <div class="subj-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                                    <span style="font-weight:600;color:white;">{{ $subject->subject }}</span>
                                </div>
                            </td>
                            <td>
                                @if($subject->pattern)
                                    <span style="font-family:monospace;font-size:0.75rem;color:#fcd34d;background:rgba(251,191,36,0.1);padding:0.2rem 0.5rem;border-radius:0.3rem;border:1px solid rgba(251,191,36,0.2);">{{ $subject->pattern }}</span>
                                @else
                                    <span style="font-size:0.75rem;color:rgba(148,163,184,0.5);">— (Búsqueda por defecto)</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <form action="{{ route('admin.platforms.subjects.destroy', [$platform, $subject]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 rounded-lg bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 transition duration-300 border border-red-500/20" 
                                            onclick="return confirm('¿Eliminar esta regla de búsqueda?')" 
                                            title="Eliminar regla">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align:center;padding:3rem 1rem;background:rgba(255,255,255,0.02);border-radius:1rem;border:1px dashed rgba(255,255,255,0.1);">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:rgba(148,163,184,0.4);margin:0 auto 1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <p style="font-size:1rem;font-weight:700;color:rgba(226,232,240,0.8);margin-bottom:0.25rem;">Sin reglas de búsqueda configuradas</p>
                <p style="font-size:0.875rem;color:rgba(148,163,184,0.6);">Añade asuntos de email para que el sistema sepa cómo identificar los códigos de esta plataforma.</p>
            </div>
        @endif
    </div>
</div>
@endsection
