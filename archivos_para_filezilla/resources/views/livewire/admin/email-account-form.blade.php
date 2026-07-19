<div class="max-w-4xl mx-auto space-y-6">

{{-- ════════════════════════════════════════════════════
     EMAIL ACCOUNT FORM — UI-* SYSTEM (GOD LEVEL)
════════════════════════════════════════════════════ --}}
<style>
/* ── FORM SPECIFIC STYLES ── */
.ui-form-hero {
    position:relative;overflow:hidden;
    background:linear-gradient(135deg,rgba(15,10,40,0.95) 0%,rgba(20,10,35,0.98) 100%);
    border:1px solid rgba(168,85,247,0.2);border-radius:1.5rem;padding:2rem 2.5rem;
}
.ui-form-hero::before {
    content:'';position:absolute;top:0;left:0;right:0;height:2px;
    background:linear-gradient(90deg,transparent,var(--ui-purple),var(--ui-pink),transparent);
}
.ui-form-hero::after {
    content:'';position:absolute;top:-50px;right:-50px;width:200px;height:200px;
    background:radial-gradient(circle,rgba(124,58,237,0.1) 0%,transparent 70%);pointer-events:none;
}

/* ── USER PICKER ── */
.ui-picker-search { width:100%;padding:0.625rem 1rem 0.625rem 2.25rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:0.75rem;color:white;font-size:0.85rem;outline:none;transition:border-color 0.2s; }
.ui-picker-search:focus { border-color:rgba(168,85,247,0.4); }
.ui-picker-search::placeholder { color:rgba(100,116,139,0.5); }

.ui-picker-item { display:flex;align-items:center;gap:0.75rem;padding:0.75rem;border-radius:0.75rem;cursor:pointer;transition:background 0.15s;border:1px solid transparent; }
.ui-picker-item:hover { background:rgba(168,85,247,0.06);border-color:rgba(168,85,247,0.15); }
.ui-picker-item.selected { background:rgba(168,85,247,0.1);border-color:rgba(168,85,247,0.3); }

.ui-picker-avatar { width:2rem;height:2rem;border-radius:0.5rem;background:linear-gradient(135deg,var(--ui-violet),var(--ui-pink));display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:900;color:white;flex-shrink:0; }

/* ── SELECTED USER CHIPS ── */
.ui-sel-chip { display:inline-flex;align-items:center;gap:0.4rem;font-size:0.72rem;font-weight:700;padding:0.3rem 0.5rem 0.3rem 0.625rem;border-radius:9999px;background:rgba(168,85,247,0.12);border:1px solid rgba(168,85,247,0.3);color:#c4b5fd; }
.ui-sel-chip button { background:none;border:none;color:rgba(196,181,253,0.6);cursor:pointer;font-size:1rem;line-height:1;padding:0;transition:color 0.15s; }
.ui-sel-chip button:hover { color:var(--ui-error-2); }
</style>

{{-- ══ HERO PAGE HEADER ══ --}}
<div class="ui-form-hero ui-anim-in">
    <a wire:navigate href="{{ route('admin.email-accounts.index') }}"
       style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.8rem;font-weight:600;color:rgba(148,163,184,0.6);text-decoration:none;margin-bottom:1rem;transition:color 0.2s;position:relative;z-index:1;"
       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.6)'">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver a Cuentas de Correo
    </a>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;position:relative;z-index:1;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.65rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--ui-purple);background:rgba(168,85,247,0.08);border:1px solid rgba(168,85,247,0.2);border-radius:9999px;padding:0.3rem 0.875rem;margin-bottom:0.75rem;">
                <svg width="10" height="10" fill="var(--ui-purple)" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
                {{ $isEditMode ? 'Editar Registro' : 'Nuevo Registro' }}
            </div>
            <h1 class="font-black text-3xl tracking-tight bg-gradient-to-br from-slate-200 via-[var(--ui-purple)] to-[var(--ui-pink)] bg-clip-text text-transparent">
                {{ $isEditMode ? 'Editar Cuenta de Correo' : 'Nueva Cuenta de Correo' }}
            </h1>
            <p style="font-size:0.875rem;color:rgba(148,163,184,0.55);margin-top:0.4rem;">
                {{ $isEditMode ? 'Modifica la configuración IMAP y los usuarios asignados.' : 'Registra una cuenta IMAP y asigna los usuarios que la usarán.' }}
            </p>
        </div>
        @if($autoConfigMessage)
        <div style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.72rem;font-weight:700;padding:0.45rem 1rem;border-radius:9999px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:var(--ui-success-2);animation:ui-fade-in 0.4s ease;z-index:1;position:relative;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            {{ $autoConfigMessage }}
        </div>
        @endif
    </div>
</div>

<form wire:submit="save" class="space-y-5">

    {{-- ══ BLOCK 1: Credentials ══ --}}
    <div class="ui-card ui-anim-in ui-delay-1" style="padding:2rem;">
        <div class="ui-sect" style="margin-bottom:1.25rem;">
            <div class="ui-sect-title" style="display:flex;align-items:center;gap:0.5rem;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Credenciales de la Cuenta
            </div>
            <div class="ui-sect-line"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="ui-label">Correo Electrónico *</label>
                <input wire:model.live.debounce.500ms="email" type="email" id="email"
                       class="ui-input {{ $errors->has('email') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="cuenta@gmail.com">
                @error('email') 
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;display:flex;align-items:center;gap:0.3rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p> 
                @enderror
            </div>

            <div>
                <label class="ui-label">Usuario IMAP *</label>
                <input wire:model="imap_username" type="text" id="imap_username"
                       class="ui-input {{ $errors->has('imap_username') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="Usualmente es el mismo correo">
                @error('imap_username') 
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;">{{ $message }}</p> 
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="ui-label">{{ $isEditMode ? 'Nueva Contraseña (dejar vacío para mantener)' : 'Contraseña IMAP *' }}</label>
                <input wire:model="password" type="password" id="password"
                       class="ui-input {{ $errors->has('password') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="{{ $isEditMode ? '••••••••' : 'Contraseña de aplicación Google / cuenta IMAP' }}">
                @if($isEditMode)
                <p style="font-size:0.72rem;color:rgba(100,116,139,0.5);margin-top:0.4rem;display:flex;align-items:center;gap:0.3rem;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    La contraseña está cifrada con AES-256. Escribe solo si deseas cambiarla.
                </p>
                @else
                <p style="font-size:0.72rem;color:rgba(168,85,247,0.6);margin-top:0.4rem;display:flex;align-items:center;gap:0.3rem;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Para Gmail, usa una <strong>Contraseña de Aplicación</strong> (16 caracteres, sin espacios).
                </p>
                @endif
                @error('password') 
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;">{{ $message }}</p> 
                @enderror
            </div>
        </div>
    </div>

    {{-- ══ BLOCK 2: IMAP Config ══ --}}
    <div class="ui-card ui-anim-in ui-delay-2" style="padding:2rem;">
        <div class="ui-sect" style="margin-bottom:1.25rem;">
            <div class="ui-sect-title" style="display:flex;align-items:center;gap:0.5rem;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                Configuración IMAP
            </div>
            <div class="ui-sect-line"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2">
                <label class="ui-label">Host / Servidor IMAP *</label>
                <input wire:model="imap_host" type="text" id="imap_host"
                       class="ui-input {{ $errors->has('imap_host') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="imap.gmail.com">
                @error('imap_host') 
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;">{{ $message }}</p> 
                @enderror
            </div>
            <div>
                <label class="ui-label">Puerto *</label>
                <input wire:model="imap_port" type="number" id="imap_port"
                       class="ui-input {{ $errors->has('imap_port') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="993">
                @error('imap_port') 
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;">{{ $message }}</p> 
                @enderror
            </div>
            <div class="md:col-span-3">
                <label class="ui-label">Encriptación</label>
                <select wire:model="imap_encryption" class="ui-filter-select w-full" style="padding:0.875rem 1.125rem;">
                    <option value="ssl">🔒 SSL/TLS — Puerto 993 (Recomendado)</option>
                    <option value="tls">🔐 STARTTLS — Puerto 587</option>
                    <option value="none">⚠️ Sin encriptación — Puerto 143</option>
                </select>
            </div>
        </div>

        {{-- Active toggle --}}
        <div style="margin-top:1.5rem;padding:1rem 1.25rem;border-radius:0.875rem;background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;gap:0.875rem;">
            <label class="ui-toggle-wrap">
                <input wire:model="is_active" type="checkbox" id="is_active" class="ui-toggle-inp">
                <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
            </label>
            <div>
                <label for="is_active" style="font-size:0.875rem;font-weight:700;color:white;cursor:pointer;">Cuenta Activa</label>
                <p style="font-size:0.72rem;color:rgba(148,163,184,0.5);margin-top:0.1rem;">El sistema usará esta cuenta al buscar códigos automáticamente.</p>
            </div>
        </div>
    </div>

    {{-- ══ BLOCK 3: User Assignment ══ --}}
    <div class="ui-card ui-anim-in ui-delay-3" style="border-color:rgba(168,85,247,0.15);padding:2rem;">
        <div class="ui-sect" style="margin-bottom:1.25rem;">
            <div class="ui-sect-title" style="display:flex;align-items:center;gap:0.5rem;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Usuarios Asignados
            </div>
            <div class="ui-sect-line"></div>
        </div>

        <p style="font-size:0.82rem;color:rgba(148,163,184,0.55);margin-bottom:1.25rem;line-height:1.6;">
            Selecciona los usuarios que tendrán acceso a leer códigos a través de esta cuenta IMAP.
        </p>

        {{-- Selected user chips --}}
        @if(count($selectedUserIds) > 0)
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1rem;padding:0.875rem;background:rgba(168,85,247,0.05);border:1px solid rgba(168,85,247,0.15);border-radius:0.875rem;">
            @foreach($selectedUserIds as $uid)
            @php $selUser = $allUsers->firstWhere('id', $uid) ?? \App\Models\User::find($uid); @endphp
            @if($selUser)
            <span class="ui-sel-chip">
                <span style="width:1.25rem;height:1.25rem;border-radius:50%;background:linear-gradient(135deg,var(--ui-violet),var(--ui-pink));display:inline-flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:900;color:white;">{{ strtoupper(substr($selUser->name,0,1)) }}</span>
                {{ $selUser->username ?? $selUser->name }}
                <button type="button" wire:click="removeUser({{ $uid }})" title="Quitar">&times;</button>
            </span>
            @endif
            @endforeach
        </div>
        @else
        <div style="padding:0.875rem;background:rgba(100,116,139,0.05);border:1px dashed rgba(100,116,139,0.15);border-radius:0.875rem;margin-bottom:1rem;text-align:center;">
            <p style="font-size:0.82rem;color:rgba(100,116,139,0.5);">Ningún usuario seleccionado — la cuenta funcionará de forma global.</p>
        </div>
        @endif

        {{-- User search --}}
        <div style="position:relative;margin-bottom:0.875rem;">
            <svg width="14" height="14" fill="none" stroke="rgba(168,85,247,0.5)" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.200ms="userSearch" type="text" class="ui-picker-search" placeholder="Buscar usuarios por nombre, usuario o correo...">
        </div>

        {{-- User list --}}
        <div style="max-height:280px;overflow-y:auto;display:flex;flex-direction:column;gap:0.25rem;padding-right:0.25rem;">
            @forelse($allUsers as $user)
            @php $isSelected = in_array($user->id, $selectedUserIds); @endphp
            <div class="ui-picker-item {{ $isSelected ? 'selected' : '' }}" wire:click="toggleUser({{ $user->id }})" wire:key="user-{{ $user->id }}">
                <div class="ui-picker-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.875rem;font-weight:700;color:white;">{{ $user->name }}</div>
                    <div style="font-size:0.72rem;color:rgba(148,163,184,0.5);">{{ $user->username ?? '' }} · {{ $user->email }}</div>
                </div>
                <div style="flex-shrink:0;">
                    @if($isSelected)
                    <div style="width:1.25rem;height:1.25rem;border-radius:50%;background:linear-gradient(135deg,var(--ui-violet),var(--ui-purple));display:flex;align-items:center;justify-content:center;">
                        <svg width="10" height="10" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    @else
                    <div style="width:1.25rem;height:1.25rem;border-radius:50%;border:1.5px solid rgba(148,163,184,0.25);"></div>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:2rem;color:rgba(148,163,184,0.4);font-size:0.85rem;">
                @if($userSearch)
                No se encontraron usuarios con "{{ $userSearch }}"
                @else
                No hay usuarios activos disponibles
                @endif
            </div>
            @endforelse
        </div>
        @if($allUsers->count() > 0)
        <p style="font-size:0.7rem;color:rgba(100,116,139,0.4);margin-top:0.75rem;">{{ count($selectedUserIds) }} seleccionado(s) de {{ $allUsers->total() ?? $allUsers->count() }} usuarios activos</p>
        @endif
    </div>

    {{-- ══ ACTION BUTTONS ══ --}}
    <div class="ui-card ui-anim-in ui-delay-3" style="padding:1.5rem;">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="w-full sm:w-2/5">
                <a wire:navigate href="{{ route('admin.email-accounts.index') }}" class="ui-btn-secondary w-full justify-center" style="padding:0.9rem 2rem;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Cancelar
                </a>
            </div>
            <div class="w-full sm:w-3/5">
                <button type="submit" class="ui-btn ui-btn-primary w-full justify-center" wire:loading.attr="disabled" style="padding:0.9rem 2rem;">
                    <span wire:loading.remove wire:target="save" style="display:flex;align-items:center;gap:0.5rem;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $isEditMode ? 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4' : 'M12 4v16m8-8H4' }}"/></svg>
                        {{ $isEditMode ? 'Guardar Cambios' : 'Crear Cuenta' }}
                    </span>
                    <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:0.5rem;">
                        <svg class="animate-spin" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </div>
    </div>

</form>
</div>
