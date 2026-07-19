@extends('admin.layouts.app')

@section('title', 'Nuevo Plan de Franquicia - Panel de Administración')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- BACK + HEADER (HERO STYLE) --}}
    <div class="ui-anim-in">
        <a href="{{ route('admin.franchise-plans.index') }}" wire:navigate class="ui-back-link">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Planes de Franquicia
        </a>

        <div class="ui-hero">
            <div>
                <div class="ui-hero-tag">
                    Nuevo Registro
                </div>
                <h1 class="ui-hero-title">Crear Plan de Franquicia</h1>
                <p class="ui-hero-sub">Define los límites y características de un nuevo paquete para revendedores.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:0.75rem; padding:1rem; margin-bottom:1.5rem;">
            <ul style="color:#f87171; font-size:0.85rem; margin-left:1.5rem; list-style:disc;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.franchise-plans.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- BLOQUE 1: Configuración del Plan --}}
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        </div>
                        Configuración del Plan
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="name" class="ui-label">Nombre del Plan *</label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                               class="ui-input {{ $errors->has('name') ? 'ui-input-error' : '' }}"
                               placeholder="Ej: Plan Básico, Pro, Master..." autofocus>
                    </div>

                    <div>
                        <label for="price" class="ui-label">Precio Sugerido (Referencial)</label>
                        <div style="position:relative;">
                            <div style="position:absolute;top:0;bottom:0;left:0;padding-left:1.125rem;display:flex;align-items:center;pointer-events:none;">
                                <span style="color:rgba(148,163,184,0.6);font-weight:700;">$</span>
                            </div>
                            <input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}"
                                   class="ui-input" style="padding-left:2.5rem;"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label for="max_clients" class="ui-label">Máximo de Clientes</label>
                        <input type="number" id="max_clients" name="max_clients" min="1" value="{{ old('max_clients') }}"
                               class="ui-input" placeholder="Dejar en blanco para ilimitado">
                        <p style="font-size:0.75rem;color:rgba(148,163,184,0.5);margin-top:0.4rem;">Cantidad de clientes que el revendedor podrá registrar.</p>
                    </div>
                    
                    <div>
                        <label for="max_queries_per_day_per_client" class="ui-label">Consultas diarias por Cliente *</label>
                        <input type="number" id="max_queries_per_day_per_client" name="max_queries_per_day_per_client" required min="1" value="{{ old('max_queries_per_day_per_client', 100) }}"
                               class="ui-input {{ $errors->has('max_queries_per_day_per_client') ? 'ui-input-error' : '' }}">
                        <p style="font-size:0.75rem;color:rgba(148,163,184,0.5);margin-top:0.4rem;">Límite de peticiones al día por cada cliente.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- BLOQUE 2: Características --}}
        <div class="ae-card ui-anim-in ui-delay-2">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        Características del Plan
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <label class="ui-label">Detalles Incluidos</label>
                <p style="font-size:0.75rem;color:rgba(148,163,184,0.5);margin-bottom:1rem;margin-top:0.2rem;">Agrega las funciones clave que ofrece este plan (ej: Soporte 24/7, Servidor Dedicado).</p>
                
                <div style="display:flex;gap:0.75rem;margin-bottom:1.5rem;height:2.8rem;">
                    <input type="text" id="feature_input" class="ui-input" style="flex:1;height:100%;margin:0;" placeholder="Escribe una nueva característica y pulsa añadir...">
                    <button type="button" onclick="addFeature()" class="ui-btn ui-btn-primary" style="height:100%; border-radius:0.75rem; padding:0 1.5rem; font-weight:700;">Añadir</button>
                </div>
                
                <ul id="features_list" style="display:flex;flex-direction:column;gap:0.5rem;padding:0;margin:0;list-style:none;">
                    <!-- Se llenará con JS -->
                </ul>
            </div>
        </div>

        {{-- BLOQUE 3: Estado --}}
        <div class="ae-card ui-anim-in ui-delay-3">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Visibilidad
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div style="padding:1.5rem; border-radius:0.875rem; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:1.25rem;">
                        <label class="ui-toggle-wrap">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="ui-toggle-inp">
                            <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                        </label>
                        <div>
                            <label for="is_active" style="font-size:1rem;font-weight:700;color:white;cursor:pointer;">Plan Activo</label>
                            <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);margin-top:0.2rem;">Disponible para ser asignado a nuevos revendedores.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="ui-anim-in ui-delay-3 ui-form-actions">
            <a href="{{ route('admin.franchise-plans.index') }}" wire:navigate class="ui-btn ui-btn-secondary ui-btn-large" style="flex:1;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> 
                Cancelar
            </a>
            <button type="submit" class="ui-btn ui-btn-primary ui-btn-large" style="flex:2;">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> 
                Crear Plan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let featureIndex = 0;
    
    function addFeature() {
        const input = document.getElementById('feature_input');
        const val = input.value.trim();
        if(!val) return;
        
        const list = document.getElementById('features_list');
        const li = document.createElement('li');
        li.style.background = 'rgba(255,255,255,0.03)';
        li.style.border = '1px solid rgba(255,255,255,0.08)';
        li.style.borderRadius = '0.75rem';
        li.style.padding = '0.75rem 1rem';
        li.style.display = 'flex';
        li.style.justifyContent = 'space-between';
        li.style.alignItems = 'center';
        
        li.innerHTML = `
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span style="font-size:0.875rem;font-weight:600;color:white;">${val}</span>
                <input type="hidden" name="features[]" value="${val}">
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="color:rgba(244,63,94,0.7);background:none;border:none;cursor:pointer;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        `;
        list.appendChild(li);
        input.value = '';
        input.focus();
    }
    
    document.getElementById('feature_input').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            addFeature();
        }
    });
</script>
@endpush
@endsection
