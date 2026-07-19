@extends('admin.layouts.app')

@section('title', 'Nuevo Usuario - Panel de Administración')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- BACK + HEADER (HERO STYLE) --}}
    <div class="ui-anim-in">
        <a href="{{ route('admin.users.index') }}" wire:navigate class="ui-back-link">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Usuarios
        </a>

        <div class="ui-hero">
            <div>
                <div class="ui-hero-tag">
                    Creación de Cuenta
                </div>
                <h1 class="ui-hero-title">Nuevo Usuario (Staff)</h1>
                <p class="ui-hero-sub">Agrega un administrador o revendedor a la plataforma y define sus niveles de acceso.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- â•â• BLOQUE 1: Informacií³n de Acceso â•â• --}}
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        Información de Acceso
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="username" class="ui-label">Usuario / Alias *</label>
                        <input type="text" id="username" name="username" required value="{{ old('username') }}"
                               class="ui-input {{ $errors->has('username') ? 'ui-input-error' : '' }}"
                               placeholder="ej. superadmin" autofocus>
                        @error('username')
                            <p class="ui-error-msg">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="ui-label">Correo Electrónico *</label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                               class="ui-input {{ $errors->has('email') ? 'ui-input-error' : '' }}"
                               placeholder="correo@ejemplo.com">
                        @error('email')
                            <p class="ui-error-msg">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label for="password" class="ui-label">Contraseña *</label>
                        <input type="password" id="password" name="password" required min="6"
                               class="ui-input {{ $errors->has('password') ? 'ui-input-error' : '' }}"
                               placeholder="Mínimo 6 caracteres">
                        @error('password')
                            <p class="ui-error-msg">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- â•â• BLOQUE 2: Perfil y Permisos â•â• --}}
        <div class="ae-card ui-anim-in ui-delay-2">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                        Perfil y Permisos
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="name" class="ui-label">Nombre Completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="ui-select" placeholder="Juan Pérez">
                    </div>

                    <div>
                        <label for="phone" class="ui-label">Teléfono</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               class="ui-select" placeholder="+57 300 1234567">
                    </div>
                </div>

                @if(auth()->id() === 1)
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label for="role" class="ui-label">Nivel de Acceso (Rol) *</label>
                        <select id="role" name="role" required class="ui-select" onchange="togglePlan(this.value)">
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Franquicia (Admin Dueño)</option>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Revendedor (Staff)</option>
                        </select>
                    </div>
                    
                    <div id="plan_container" class="{{ old('role') === 'user' ? 'hidden' : 'block' }}">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div>
                                <label for="plan_id" class="ui-label">Plan de Franquicia</label>
                                <select id="plan_id" name="plan_id" class="ui-select">
                                    <option value="">-- Sin plan asignado (Ilimitado) --</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} @if($plan->max_clients) (Máx {{ $plan->max_clients }} clientes) @else (Ilimitado) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <p class="ui-hint-msg">Asigna un límite de clientes para esta Franquicia.</p>
                            </div>

                            <div>
                                <label for="subscription_ends_at" class="ui-label">Fecha de Vencimiento</label>
                                <input type="date" id="subscription_ends_at" name="subscription_ends_at" 
                                       value="{{ old('subscription_ends_at') }}"
                                       class="ui-input {{ $errors->has('subscription_ends_at') ? 'ui-input-error' : '' }}">
                                <p class="ui-hint-msg">Opcional. Si lo dejas en blanco, nunca expira.</p>
                                @error('subscription_ends_at')
                                    <p class="ui-error-msg">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="grace_days" class="ui-label">Días de Gracia (Tolerancia)</label>
                                <input type="number" id="grace_days" name="grace_days" min="0" max="30"
                                       value="{{ old('grace_days', 0) }}"
                                       class="ui-input {{ $errors->has('grace_days') ? 'ui-input-error' : '' }}">
                                <p class="ui-hint-msg">Días extra antes de bloquear el servicio si ya venció.</p>
                                @error('grace_days')
                                    <p class="ui-error-msg">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- â•â• BLOQUE 3: Estado â•â• --}}
        <div class="ae-card ui-anim-in ui-delay-3">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        Estado del Usuario
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="padding:1.5rem; border-radius:0.875rem; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:1.25rem;">
                    <label class="ui-toggle-wrap">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="ui-toggle-inp">
                        <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                    </label>
                    <div>
                        <label for="is_active" style="font-size:1rem;font-weight:700;color:white;cursor:pointer;">Cuenta Activa</label>
                        <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);margin-top:0.2rem;">Si desactivas esto, el usuario no podrá iniciar sesión en la plataforma.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── BOTONES ── --}}
        <div class="ui-anim-in ui-delay-3 ui-form-actions">
            <a href="{{ route('admin.users.index') }}" wire:navigate class="ui-btn ui-btn-secondary ui-btn-large" style="flex:1;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> 
                Cancelar
            </a>
            <button type="submit" class="ui-btn ui-btn-primary ui-btn-large" style="flex:2;">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> 
                Crear Usuario
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    function togglePlan(role) {
        const planContainer = document.getElementById('plan_container');
        if (planContainer) {
            if (role === 'admin') {
                planContainer.style.display = 'block';
            } else {
                planContainer.style.display = 'none';
            }
        }
    }

    // Asegurar el estado inicial
    document.addEventListener('DOMContentLoaded', () => {
        const roleSelect = document.getElementById('role');
        if(roleSelect) {
            togglePlan(roleSelect.value);
        }
    });
</script>
@endpush
@endsection
