@extends('admin.layouts.app')
@section('title', 'Editar Correo Autorizado - Panel de Administración')
@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- BACK + HEADER (HERO STYLE) --}}
    <div class="ui-anim-in">
        <a href="{{ route('admin.allowed-emails.index') }}" wire:navigate class="ui-back-link">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Correos Autorizados
        </a>

        <div class="ui-hero">
            <div>
                <div class="ui-hero-tag">
                    Edición de Registro
                </div>
                <h1 class="ui-hero-title">Editar Correo</h1>
                <p class="ui-hero-sub">Modifica los permisos o asignaciones de este correo.</p>
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

    <form action="{{ route('admin.allowed-emails.update', $allowedEmail) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- BLOQUE 1: Información del Correo --}}
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        Información del Correo
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="email" class="ui-label">Correo Electrónico *</label>
                        <input type="email" id="email" name="email" required value="{{ old('email', $allowedEmail->email) }}"
                               class="ui-input" autofocus>
                    </div>

                    <div>
                        <label for="description" class="ui-label">Descripción</label>
                        <input type="text" id="description" name="description" value="{{ old('description', $allowedEmail->description) }}"
                               class="ui-input">
                    </div>
                </div>
            </div>
        </div>

        {{-- BLOQUE 2: Asignación y Plataforma --}}
        <div class="ae-card ui-anim-in ui-delay-2">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        Asignación y Plataforma
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label for="platform_id" class="ui-label">Plataforma</label>
                        <select id="platform_id" name="platform_id" class="ui-select">
                            <option value="">Sin plataforma</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}" {{ old('platform_id', $allowedEmail->platform_id) == $platform->id ? 'selected' : '' }}>{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="email_account_id" class="ui-label">Servidor IMAP</label>
                        <select id="email_account_id" name="email_account_id" class="ui-select">
                            <option value="">Automático (busca en todos)</option>
                            @foreach($emailAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('email_account_id', $allowedEmail->email_account_id) == $account->id ? 'selected' : '' }}>{{ $account->email }}</option>
                            @endforeach
                        </select>
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
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        Estado y Permisos
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div style="padding:1.5rem; border-radius:0.875rem; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:1.25rem;">
                        <label class="ui-toggle-wrap">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $allowedEmail->is_active) ? 'checked' : '' }} class="ui-toggle-inp">
                            <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                        </label>
                        <div>
                            <label for="is_active" style="font-size:1rem;font-weight:700;color:white;cursor:pointer;">Correo Activo</label>
                            <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);margin-top:0.2rem;">Permite que el correo sea verificado.</p>
                        </div>
                    </div>

                    <div style="padding:1.5rem; border-radius:0.875rem; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:1.25rem;">
                        <label class="ui-toggle-wrap">
                            <input type="hidden" name="is_public" value="0">
                            <input type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public', $allowedEmail->is_public) ? 'checked' : '' }} class="ui-toggle-inp">
                            <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                        </label>
                        <div>
                            <label for="is_public" style="font-size:1rem;font-weight:700;color:white;cursor:pointer;">Consulta Pública</label>
                            <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);margin-top:0.2rem;">Visible para consultas sin API key.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="ui-anim-in ui-delay-3 ui-form-actions">
            <a href="{{ route('admin.allowed-emails.index') }}" wire:navigate class="ui-btn ui-btn-secondary ui-btn-large" style="flex:1;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> 
                Cancelar
            </a>
            <button type="submit" class="ui-btn ui-btn-primary ui-btn-large" style="flex:2;">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> 
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
