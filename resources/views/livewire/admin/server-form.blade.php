<div class="max-w-4xl mx-auto space-y-6">

{{-- ════════════════════════════════════════════════════
     SERVERS FORM — UI-* SYSTEM (GOD LEVEL)
════════════════════════════════════════════════════ --}}
<style>
/* HACKER TERMINAL */
.ui-term { background:#02040f;border:1px solid rgba(168,85,247,0.2);border-radius:1rem;padding:1.5rem;font-family:'Fira Code','Consolas',monospace;font-size:0.82rem;color:#94a3b8;margin-top:1.25rem;box-shadow:inset 0 0 30px rgba(0,0,0,0.6); }
.ui-term-step { display:flex;align-items:flex-start;gap:0.5rem;margin-bottom:0.5rem;animation:ui-fade-in 0.3s ease-out; }
.ui-term-prefix { color:#a855f7;font-weight:900; }
.ui-term-ok  { color:#10b981;font-weight:700; }
.ui-term-err { color:#ef4444;font-weight:700; }
@keyframes ui-fade-in { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:translateY(0)} }
</style>

{{-- BACK + HEADER --}}
<div class="ui-anim-in">
    <a wire:navigate href="{{ route('admin.servers.index') }}"
       style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.8rem;font-weight:600;color:rgba(148,163,184,0.6);text-decoration:none;margin-bottom:1.25rem;transition:color 0.2s;"
       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.6)'">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver a Servidores
    </a>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:1.875rem;font-weight:900;color:white;tracking:tight;line-height:1.15;">
                {{ $isEditMode ? 'Editar Servidor IMAP' : 'Registrar Servidor IMAP' }}
            </h1>
            <p style="font-size:0.875rem;color:rgba(148,163,184,0.5);margin-top:0.4rem;">
                {{ $isEditMode ? 'Modifica la configuración del servidor y prueba la conexión.' : 'Agrega un servidor de correo para la lectura automática de códigos.' }}
            </p>
        </div>
        {{-- Provider auto-detect chip --}}
        @if($autoConfigMessage)
        <div class="ui-badge ui-badge--success" style="padding:0.35rem 0.875rem;animation:ui-fade-in 0.4s ease;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            {{ $autoConfigMessage }}
        </div>
        @endif
    </div>
</div>

<form wire:submit="save" class="space-y-5">

    {{-- ══ BLOQUE 1: Credenciales ══ --}}
    <div class="ae-card ui-anim-in ui-delay-1">
        <div class="ae-card-head">
            <div class="ae-card-title">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ui-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    Credenciales de la Cuenta
                </div>
            </div>
        </div>
        <div class="ae-card-body">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Email --}}
            <div>
                <label class="ui-label">Correo Electrónico *</label>
                <input wire:model.live.debounce.500ms="email" type="email" id="email"
                       class="ui-input {{ $errors->has('email') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="correo@gmail.com">
                @error('email')
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;display:flex;align-items:center;gap:0.3rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- IMAP Username --}}
            <div>
                <label class="ui-label">Usuario IMAP *</label>
                <input wire:model="imap_username" type="text" id="imap_username"
                       class="ui-input {{ $errors->has('imap_username') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="Usualmente es el mismo correo">
                @error('imap_username')
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;display:flex;align-items:center;gap:0.3rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="md:col-span-2">
                <label class="ui-label">{{ $isEditMode ? 'Nueva Contraseña IMAP (dejar vacío para mantener)' : 'Contraseña IMAP *' }}</label>
                <input wire:model="password" type="password" id="password"
                       class="ui-input {{ $errors->has('password') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="{{ $isEditMode ? '•••••••• (dejar en blanco para no cambiar)' : 'Contraseña de aplicación o cuenta' }}">
                @if($isEditMode)
                    <p style="font-size:0.72rem;color:rgba(100,116,139,0.5);margin-top:0.4rem;">🔐 Por seguridad, la contraseña actual está cifrada con AES-256 y no se muestra.</p>
                @endif
                @error('password')
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;display:flex;align-items:center;gap:0.3rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
        </div>
    </div>

    {{-- ══ BLOQUE 2: Infraestructura ══ --}}
    <div class="ae-card ui-anim-in ui-delay-2">
        <div class="ae-card-head">
            <div class="ae-card-title">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ui-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                    </div>
                    Infraestructura IMAP
                </div>
            </div>
        </div>
        <div class="ae-card-body">

        {{-- Security Warning --}}
        @if($imap_encryption === 'none')
        <div style="margin-bottom:1.25rem;padding:1rem;border-radius:0.875rem;border:1px solid rgba(245,158,11,0.3);background:rgba(245,158,11,0.07);display:flex;align-items:flex-start;gap:0.875rem;">
            <svg width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:0.1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <div style="font-size:0.8rem;font-weight:800;color:#f59e0b;margin-bottom:0.25rem;">⚠️ Advertencia de Seguridad</div>
                <div style="font-size:0.78rem;color:rgba(245,158,11,0.7);line-height:1.5;">Sin encriptación, las contraseñas viajan en texto plano por la red. Se recomienda usar <strong>SSL/TLS</strong> en el puerto <strong>993</strong>.</div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Host --}}
            <div class="md:col-span-2">
                <label class="ui-label">Servidor / Host IMAP *</label>
                <input wire:model="imap_host" type="text" id="imap_host"
                       class="ui-input {{ $errors->has('imap_host') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="imap.gmail.com">
                @error('imap_host') 
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Port --}}
            <div>
                <label class="ui-label">Puerto *</label>
                <input wire:model="imap_port" type="number" id="imap_port"
                       class="ui-input {{ $errors->has('imap_port') ? 'border-[var(--ui-error)]' : '' }}"
                       placeholder="993">
                @error('imap_port') 
                    <p class="ui-error" style="font-size:0.75rem;color:var(--ui-error-2);margin-top:0.4rem;">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Encryption --}}
            <div class="md:col-span-3">
                <label class="ui-label">Protocolo de Encriptación</label>
                <select wire:model.live="imap_encryption" id="imap_encryption" class="ui-filter-select w-full" style="padding:0.875rem 1.125rem;">
                    <option value="ssl">🔒 SSL/TLS — Puerto 993 (Recomendado)</option>
                    <option value="tls">🔐 STARTTLS — Puerto 587</option>
                    <option value="none">⚠️ Sin encriptación — Puerto 143 (Inseguro)</option>
                </select>
            </div>
        </div>

        {{-- Toggle Activo --}}
        <div style="margin-top:1.5rem;padding:1rem 1.25rem;border-radius:0.875rem;background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;gap:0.875rem;">
            <label class="ui-toggle-wrap">
                <input wire:model="is_active" type="checkbox" id="is_active" class="ui-toggle-inp">
                <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
            </label>
            <div>
                <label for="is_active" style="font-size:0.875rem;font-weight:700;color:white;cursor:pointer;">Servidor Activo</label>
                <p style="font-size:0.72rem;color:rgba(148,163,184,0.5);margin-top:0.1rem;">Cuando está activo, el sistema busca códigos en este buzón automáticamente.</p>
            </div>
        </div>
        </div>
    </div>

    {{-- ══ BLOQUE 3: Diagnóstico Hacker Terminal ══ --}}
    <div class="ae-card ui-anim-in ui-delay-3" style="border-color:rgba(168,85,247,0.2);">
        <div class="ae-card-head">
            <div class="ae-card-title">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ui-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    Diagnóstico de Conexión IMAP
                </div>
            </div>
        </div>
        <div class="ae-card-body">

        <div class="flex flex-col md:flex-row gap-5 items-start">
            <div style="flex:1;">
                <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);line-height:1.6;">Verifica que la conexión funcione antes de guardar. La consola mostrará cada paso de la negociación SSL y autenticación en tiempo real.</p>
            </div>
            <div style="flex-shrink:0;width:100%;max-width:220px;">
                <button type="button" wire:click="testConnection" wire:loading.attr="disabled" class="ui-btn-ghost w-full justify-center" style="border-color:rgba(168,85,247,0.3);color:var(--ui-purple);">
                    <span wire:loading.remove wire:target="testConnection" style="display:flex;align-items:center;gap:0.5rem;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Probar Conexión
                    </span>
                    <span wire:loading wire:target="testConnection" style="display:flex;align-items:center;gap:0.5rem;">
                        <svg class="animate-spin" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Verificando...
                    </span>
                </button>
            </div>
        </div>

        {{-- Terminal --}}
        @if(count($diagnosticSteps) > 0 || $isDiagnosing)
        <div class="ui-term">
            {{-- Window Bar --}}
            <div style="display:flex;align-items:center;gap:0.4rem;margin-bottom:1rem;padding-bottom:0.75rem;border-bottom:1px solid rgba(168,85,247,0.15);">
                <div style="width:10px;height:10px;border-radius:50%;background:#ef4444;"></div>
                <div style="width:10px;height:10px;border-radius:50%;background:#f59e0b;"></div>
                <div style="width:10px;height:10px;border-radius:50%;background:#10b981;"></div>
                <span style="margin-left:0.5rem;font-size:0.65rem;font-weight:700;color:rgba(168,85,247,0.6);letter-spacing:0.15em;text-transform:uppercase;">IMAP_DIAGNOSTIC_CONSOLE</span>
            </div>

            {{-- Steps --}}
            @foreach($diagnosticSteps as $step)
            <div class="ui-term-step">
                <span class="ui-term-prefix">›</span>
                <span class="{{ $step['type'] === 'success' ? 'ui-term-ok' : ($step['type'] === 'error' ? 'ui-term-err' : '') }}">{{ $step['msg'] }}</span>
            </div>
            @endforeach

            {{-- Blinking cursor --}}
            @if($isDiagnosing)
            <div class="ui-term-step animate-pulse">
                <span class="ui-term-prefix">›</span>
                <span style="color:rgba(148,163,184,0.3);">_</span>
            </div>
            @endif

            {{-- Result Block --}}
            @if($diagnosticResult === 'success')
            <div style="margin-top:1rem;padding:0.875rem;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:0.625rem;color:#34d399;">
                <div style="font-weight:800;margin-bottom:0.25rem;">[OK] {{ $diagnosticMessage }}</div>
                <div style="font-size:0.72rem;opacity:0.7;">
                    Host: {{ $diagnosticData['host'] ?? '' }}:{{ $diagnosticData['port'] ?? '' }} &nbsp;|&nbsp;
                    Encriptación: {{ strtoupper($diagnosticData['encryption'] ?? '') }} &nbsp;|&nbsp;
                    Mensajes en bandeja: {{ $diagnosticData['messages'] ?? 0 }}
                </div>
            </div>
            @elseif($diagnosticResult === 'error')
            <div style="margin-top:1rem;padding:0.875rem;background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.2);border-radius:0.625rem;color:#f87171;">
                <div style="font-weight:800;margin-bottom:0.25rem;">[FAIL] {{ $diagnosticMessage }}</div>
                <div style="font-size:0.72rem;opacity:0.7;word-break:break-all;">{{ $diagnosticData['error'] ?? '' }}</div>
            </div>
            @endif
        </div>
        @endif
        </div>
    </div>

    {{-- ══ ACTIONS ══ --}}
    <div class="ui-anim-in ui-delay-4 ui-form-actions" style="padding-bottom: 4rem;">
        <a wire:navigate href="{{ route('admin.servers.index') }}" class="ui-btn ui-btn-secondary" style="justify-content:center; flex: 1;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Cancelar
        </a>
        <button type="submit" class="ui-btn ui-btn-primary" wire:loading.attr="disabled" style="justify-content:center; flex: 2;">
            <span wire:loading.remove wire:target="save" style="display:flex;align-items:center;gap:0.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $isEditMode ? 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4' : 'M12 4v16m8-8H4' }}"/></svg>
                {{ $isEditMode ? 'Guardar Cambios' : 'Registrar Servidor' }}
            </span>
            <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:0.5rem;">
                <svg class="animate-spin" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Guardando...
            </span>
        </button>
    </div>

</form>
</div>
