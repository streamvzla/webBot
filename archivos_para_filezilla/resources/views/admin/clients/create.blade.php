@extends('admin.layouts.app')

@section('title', 'Nuevo Cliente - Panel de Administración')

@section('content')
<style>
    .access-radio:checked + .access-card {
        background: rgba(168, 85, 247, 0.1) !important;
        border-color: #a855f7 !important;
        box-shadow: 0 0 15px rgba(168, 85, 247, 0.2) !important;
    }
    .platform-checkbox:checked + .platform-card {
        background: rgba(168, 85, 247, 0.1) !important;
        border-color: #a855f7 !important;
        box-shadow: 0 0 15px rgba(168, 85, 247, 0.2) !important;
    }
    .platform-checkbox:checked ~ .check-icon {
        opacity: 1 !important;
        transform: scale(1) !important;
    }
</style>
<div class="max-w-4xl mx-auto space-y-8">

    {{-- BACK + HEADER (HERO STYLE) --}}
    <div class="ui-anim-in">
        <a href="{{ route('admin.clients.index') }}" wire:navigate class="ui-back-link">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Clientes
        </a>

        <div class="ui-hero">
            <div>
                <div class="ui-hero-tag">
                    Creación de Cuenta
                </div>
                <h1 class="ui-hero-title">Registrar Cliente</h1>
                <p class="ui-hero-sub">Agrega un nuevo consumidor final al sistema para asignarle límites y plataformas.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.clients.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- ── BLOQUE 1: Información Personal ── --}}
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        Información Personal
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="name" class="ui-label">Nombre del Cliente *</label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                               class="ui-input {{ $errors->has('name') ? 'ui-input-error' : '' }}"
                               placeholder="Ej: Juan Pérez" autofocus>
                        @error('name')
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
                               placeholder="cliente@dominio.com">
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
                        <input type="password" id="password" name="password" required min="8"
                               class="ui-input {{ $errors->has('password') ? 'ui-input-error' : '' }}"
                               placeholder="Mínimo 8 caracteres">
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

        {{-- ── BLOQUE 2: Configuración de Consultas ── --}}
        <div class="ae-card ui-anim-in ui-delay-2">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        Configuración de Consultas
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label for="max_queries_per_day" class="ui-label">Límite Diario de Consultas</label>
                        <input type="number" id="max_queries_per_day" name="max_queries_per_day" value="{{ old('max_queries_per_day', 100) }}" min="1"
                               class="ui-input {{ $errors->has('max_queries_per_day') ? 'ui-input-error' : '' }}">
                        @error('max_queries_per_day')
                            <p class="ui-error-msg">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div style="display:flex; flex-direction:column; justify-content:center; gap:0.5rem; margin-top:0.5rem;">
                        <label class="ui-label">Estado de la Cuenta</label>
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <label class="ui-toggle-wrap">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="ui-toggle-inp" {{ old('is_active', true) ? 'checked' : '' }}>
                                <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                            </label>
                            <div>
                                <span style="display:block; font-size:0.9rem; font-weight:700; color:white;">Cliente Activo</span>
                                <span style="display:block; font-size:0.75rem; color:rgba(148,163,184,0.6);">Permitir el acceso inmediato al sistema</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── BLOQUE 3: Plataformas Asignadas ── --}}
        <div class="ae-card ui-anim-in ui-delay-3">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        Plataformas Asignadas
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                @php $selectedPlatforms = old('platforms', []); @endphp
                
                @if($platforms->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                        @foreach($platforms as $platform)
                            <label style="display: block; cursor: pointer; position: relative;">
                                <input type="checkbox" name="platforms[]" value="{{ $platform->id }}" class="platform-checkbox" style="display:none;" {{ in_array($platform->id, $selectedPlatforms) ? 'checked' : '' }}>
                                <div class="platform-card" style="padding: 1rem; background: rgba(255,255,255,0.03); border: 1.5px solid rgba(168,85,247,0.15); border-radius: 1rem; transition: all 0.2s; display: flex; align-items: center; gap: 0.75rem;">
                                    @if($platform->logo)
                                        <img src="{{ asset('storage/' . $platform->logo) }}" alt="{{ $platform->name }}" style="width:2.5rem; height:2.5rem; border-radius:0.5rem; object-fit:cover;">
                                    @else
                                        <div style="width:2.5rem; height:2.5rem; border-radius:0.5rem; background:{{ $platform->color ?? '#a855f7' }}; color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.9rem;">
                                            {{ strtoupper(substr($platform->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div style="display:flex; flex-direction:column;">
                                        <span style="color:white; font-weight:700; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $platform->name }}</span>
                                        <span style="color:rgba(148,163,184,0.6); font-size:0.7rem;">{{ $platform->max_queries_per_day ?? '∞' }} / día</span>
                                    </div>
                                </div>
                                <div class="check-icon" style="position: absolute; top: 0.5rem; right: 0.5rem; width: 1.25rem; height: 1.25rem; background: #a855f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transform: scale(0.5); transition: all 0.2s;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div style="padding: 2rem; text-align: center; border: 1px dashed rgba(168,85,247,0.3); border-radius: 1rem; background: rgba(168,85,247,0.02);">
                        <p style="color:rgba(148,163,184,0.6); font-size:0.9rem; margin-bottom:0.5rem;">No hay plataformas disponibles en el sistema.</p>
                        <a href="{{ route('admin.platforms.create') }}" style="color:#c084fc; font-weight:700; font-size:0.85rem; text-decoration:none;">Crear una plataforma ahora</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── BLOQUE 4: Permisos de Acceso a Correos ── --}}
        <div class="ae-card ui-anim-in ui-delay-4">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        Permisos de Acceso a Correos
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <label style="display: block; cursor: pointer; position: relative;">
                        <input type="radio" name="access_mode" value="all" class="access-radio" style="display:none;" {{ old('access_mode', 'all') === 'all' ? 'checked' : '' }}>
                        <div class="access-card" style="flex-direction:column; align-items:flex-start; padding: 1rem; background: rgba(255,255,255,0.03); border: 1.5px solid rgba(168,85,247,0.15); border-radius: 1rem; transition: all 0.2s; display: flex; gap: 0.25rem;">
                            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.2rem;">
                                <div style="width:1.5rem; height:1.5rem; border-radius:50%; background:rgba(52,211,153,0.15); color:#34d399; display:flex; align-items:center; justify-content:center;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span style="color:white; font-weight:800; font-size:0.95rem;">Acceso a Todo</span>
                            </div>
                            <span style="color:rgba(148,163,184,0.6); font-size:0.75rem;">Podrá consultar cualquier correo autorizado.</span>
                        </div>
                    </label>
                    <label style="display: block; cursor: pointer; position: relative;">
                        <input type="radio" name="access_mode" value="selective" class="access-radio" style="display:none;" {{ old('access_mode') === 'selective' ? 'checked' : '' }}>
                        <div class="access-card" style="flex-direction:column; align-items:flex-start; padding: 1rem; background: rgba(255,255,255,0.03); border: 1.5px solid rgba(168,85,247,0.15); border-radius: 1rem; transition: all 0.2s; display: flex; gap: 0.25rem;">
                            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.2rem;">
                                <div style="width:1.5rem; height:1.5rem; border-radius:50%; background:rgba(245,158,11,0.15); color:#fbbf24; display:flex; align-items:center; justify-content:center;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <span style="color:white; font-weight:800; font-size:0.95rem;">Acceso Selectivo</span>
                            </div>
                            <span style="color:rgba(148,163,184,0.6); font-size:0.75rem;">Solo accederá a los correos que especifiques.</span>
                        </div>
                    </label>
                </div>

                {{-- Contenedor de Correos Selectivos --}}
                <div id="emailListContainer" style="display: {{ old('access_mode') === 'selective' ? 'block' : 'none' }}; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; overflow:hidden; background:rgba(255,255,255,0.02);">
                    
                    {{-- Buscador de correos --}}
                    <div style="padding:1rem; border-bottom:1px solid rgba(255,255,255,0.06);">
                        <div style="position:relative;">
                            <svg style="position:absolute; left:0.8rem; top:50%; transform:translateY(-50%); color:rgba(148,163,184,0.5);" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" id="emailSearch" class="ui-input" placeholder="Buscar correo específico..." style="padding-left:2.2rem; margin-top:0;">
                        </div>
                    </div>

                    @php
                        $emailsByPlatform = $allowedEmails->groupBy('platform_id');
                        $selectedEmails = old('allowed_emails', []);
                    @endphp

                    @if($emailsByPlatform->count() > 0)
                        <div style="max-height: 400px; overflow-y: auto;" id="emailListWrapper">
                            @foreach($emailsByPlatform as $platformId => $emails)
                                @php $platform = $emails->first()->platform; @endphp
                                <div class="platform-group" data-platform="{{ strtolower($platform?->name ?? 'sin plataforma') }}">
                                    <div style="padding:0.75rem 1rem; background:rgba(255,255,255,0.02); border-bottom:1px solid rgba(255,255,255,0.04); display:flex; align-items:center; gap:0.75rem;">
                                        @if($platform?->logo)
                                            <img src="{{ asset('storage/' . $platform->logo) }}" style="width:1.5rem; height:1.5rem; border-radius:0.4rem; object-fit:cover;">
                                        @else
                                            <div style="width:1.5rem; height:1.5rem; border-radius:0.4rem; background:{{ $platform->color ?? '#a855f7' }}; display:flex; align-items:center; justify-content:center; color:white; font-size:0.75rem; font-weight:800;">
                                                {{ substr($platform?->name ?? 'S', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div style="color:white; font-weight:700; font-size:0.8rem;">{{ $platform?->name ?? 'Sin plataforma' }}</div>
                                            <div style="color:rgba(148,163,184,0.5); font-size:0.65rem;">{{ $emails->count() }} correos</div>
                                        </div>
                                    </div>
                                    <div style="display:flex; flex-direction:column;">
                                        @foreach($emails as $email)
                                            <label class="email-item" style="display:flex; align-items:center; gap:0.75rem; padding:0.65rem 1rem; cursor:pointer; border-bottom:1px solid rgba(255,255,255,0.03); transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)';" onmouseout="this.style.background='transparent';">
                                                <input type="checkbox" name="allowed_emails[]" value="{{ $email->id }}" style="accent-color: #a855f7; width:1rem; height:1rem; border-radius:0.25rem;" {{ in_array($email->id, $selectedEmails) ? 'checked' : '' }}>
                                                <div style="flex:1; display:flex; flex-direction:column; min-width:0;">
                                                    <span class="email-text" style="color:rgba(226,232,240,0.9); font-weight:600; font-size:0.85rem;">{{ $email->email }}</span>
                                                    @if($email->description)
                                                        <span style="color:rgba(148,163,184,0.5); font-size:0.75rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $email->description }}</span>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding: 2rem; text-align: center; color: rgba(148,163,184,0.6);">
                            <p style="font-size:0.85rem;">No hay correos registrados en el sistema.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── BOTONES DE ACCIÓN ── --}}
        <div class="ui-form-actions">
            <a href="{{ route('admin.clients.index') }}" class="ui-btn ui-btn-cancel">
                Cancelar
            </a>
            <button type="submit" class="ui-btn ui-btn-primary ui-btn-large">
                Guardar Cliente
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle de Lista de Correos
        const accessModeRadios = document.querySelectorAll('input[name="access_mode"]');
        const emailListContainer = document.getElementById('emailListContainer');
        accessModeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                emailListContainer.style.display = (this.value === 'selective') ? 'block' : 'none';
            });
        });

        // Buscador de Correos
        const searchInput = document.getElementById('emailSearch');
        if(searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const platformGroups = document.querySelectorAll('.platform-group');

                platformGroups.forEach(group => {
                    let hasVisibleItem = false;
                    const items = group.querySelectorAll('.email-item');
                    items.forEach(item => {
                        const emailText = item.querySelector('.email-text').textContent.toLowerCase();
                        if (emailText.includes(term)) {
                            item.style.display = 'flex';
                            hasVisibleItem = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    group.style.display = hasVisibleItem ? 'block' : 'none';
                });
            });
        }
    });
</script>
@endpush
