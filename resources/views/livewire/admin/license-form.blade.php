<div>
    @section('title', ($isEditing ? 'Editar Licencia' : 'Nueva Licencia') . ' - Panel de Administración')

    <div class="max-w-4xl mx-auto space-y-8">
        {{-- BACK + HEADER (HERO STYLE) --}}
        <div class="ui-anim-in">
            <a href="{{ route('admin.licenses.index') }}" wire:navigate class="ui-back-link">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver a Licencias
            </a>

            <div class="ui-hero">
                <div>
                    <div class="ui-hero-tag">
                        Gestor de Licencias
                    </div>
                    <h1 class="ui-hero-title">{{ $isEditing ? 'Editar Licencia' : 'Emitir Nueva Licencia' }}</h1>
                    <p class="ui-hero-sub">Configura la clave, el dominio autorizado y el estado de la licencia de Codebot.</p>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            
            {{-- ── BLOQUE 1: Identificación y Acceso ── --}}
            <div class="ae-card ui-anim-in ui-delay-1">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap">
                                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            Credenciales de la Licencia
                        </div>
                    </div>
                </div>

                <div class="ae-card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label class="ui-label">Clave de Licencia *</label>
                            <div style="display:flex;">
                                <input wire:model="license_key" type="text" class="ui-input {{ $errors->has('license_key') ? 'ui-input-error' : '' }}" style="color:#c084fc; font-family:monospace; border-top-right-radius:0; border-bottom-right-radius:0;" {{ $isEditing ? 'readonly' : '' }}>
                                @if(!$isEditing)
                                <button type="button" wire:click="generateKey" style="background:rgba(168,85,247,0.15); color:#c084fc; border:1px solid rgba(168,85,247,0.3); border-left:0; padding:0 1rem; border-top-right-radius:0.75rem; border-bottom-right-radius:0.75rem; font-size:0.85rem; font-weight:700; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='rgba(168,85,247,0.25)'" onmouseout="this.style.background='rgba(168,85,247,0.15)'">
                                    Generar
                                </button>
                                @endif
                            </div>
                            @error('license_key')
                                <p class="ui-error-msg">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-label">Dominio Autorizado</label>
                            <input wire:model="domain" type="text" placeholder="ejemplo.com" class="ui-input {{ $errors->has('domain') ? 'ui-input-error' : '' }}">
                            <p class="ui-hint-msg">Déjalo en blanco para auto-vincular al primer dominio que la instale.</p>
                            @error('domain')
                                <p class="ui-error-msg">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── BLOQUE 2: Información del Cliente ── --}}
            <div class="ae-card ui-anim-in ui-delay-2">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap">
                                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            Datos del Cliente (Opcional)
                        </div>
                    </div>
                </div>

                <div class="ae-card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label class="ui-label">Nombre del Cliente</label>
                            <input wire:model="client_name" type="text" class="ui-input" placeholder="Nombre de la empresa o persona">
                            @error('client_name') <p class="ui-error-msg">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="ui-label">Correo del Cliente</label>
                            <input wire:model="client_email" type="email" class="ui-input" placeholder="correo@cliente.com">
                            @error('client_email') <p class="ui-error-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="ui-label">Notas Privadas</label>
                        <textarea wire:model="notes" rows="3" class="ui-textarea" placeholder="Anotaciones internas sobre este cliente o venta..."></textarea>
                    </div>
                </div>
            </div>

            {{-- ── BLOQUE 3: Estado ── --}}
            <div class="ae-card ui-anim-in ui-delay-3">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap">
                                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            Estado de la Licencia
                        </div>
                    </div>
                </div>

                <div class="ae-card-body">
                    <div style="padding:1.5rem; border-radius:0.875rem; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05);">
                        <label class="ui-label">Selecciona el estado operativo</label>
                        <select wire:model="status" class="ui-select" style="max-width:300px;">
                            <option value="active">Activa (Operando normal)</option>
                            <option value="suspended">Suspendida (En revisión / falta de pago)</option>
                            <option value="revoked">Revocada (Cancelada definitivamente)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── BOTONES ── --}}
            <div class="ui-anim-in ui-delay-3 ui-form-actions">
                <a href="{{ route('admin.licenses.index') }}" wire:navigate class="ui-btn ui-btn-secondary ui-btn-large" style="flex:1;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> 
                    Cancelar
                </a>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-large" style="flex:2;">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> 
                    {{ $isEditing ? 'Guardar Cambios' : 'Emitir Licencia' }}
                </button>
            </div>

        </form>
    </div>
</div>
