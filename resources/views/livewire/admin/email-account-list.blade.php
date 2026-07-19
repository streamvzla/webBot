<div class="space-y-6"
     x-data="{ notif: null }"
     @notif.window="notif = $event.detail.message; setTimeout(() => notif = null, 4000)">

{{-- ════════════════════════════════════════════════════
     EMAIL ACCOUNTS LIST — UI-* SYSTEM (GOD LEVEL)
════════════════════════════════════════════════════ --}}

{{-- ══ TOAST NOTIFICATION ══ --}}
<div x-show="notif"
     class="ui-toast"
     :class="{ 'show': notif }"
     style="display:none;">
    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <span x-text="notif"></span>
</div>

{{-- ══ CONFIRM DELETE MODAL ══ --}}
@if($confirmDeleteId)
<div class="ui-modal-backdrop active">
    <div class="ui-modal ui-anim-scale">
        <div class="ui-modal-icon ui-modal-icon--danger">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="ui-modal-title">Eliminar Cuenta</h3>
        <p class="ui-modal-body">¿Estás seguro de que deseas eliminar esta cuenta permanentemente? Se eliminará la cuenta y se desasignarán todos los usuarios vinculados. Esta acción no se puede deshacer.</p>
        <div class="ui-modal-footer">
            <button wire:click="cancelDelete" class="ui-btn ui-btn-secondary">Cancelar</button>
            <button wire:click="deleteAccount" class="ui-btn ui-btn-danger">
                <span wire:loading.remove wire:target="deleteAccount">Sí, eliminar</span>
                <span wire:loading wire:target="deleteAccount">Eliminando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ══ DRAWER OVERLAY ══ --}}
@if($drawerAccountId)
<div class="ui-drawer-overlay active" wire:click="closeDrawer"></div>
@endif

{{-- ══ DETAIL DRAWER ══ --}}
<div class="ui-drawer {{ $drawerAccountId ? 'active' : '' }}">
    @if($drawerAccountId && $this->drawerAccount)
    @php $da = $this->drawerAccount; $dprov = \App\Livewire\Admin\EmailAccountList::detectProvider($da->imap_host ?? ''); @endphp

    {{-- Drawer Header --}}
    <div class="ui-drawer-header">
        <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#a855f7,#ec4899,transparent);"></div>
        <div style="display:flex;align-items:center;gap:1rem;">
            <div class="ui-provider-badge" style="width:3rem;height:3rem;font-size:1.5rem;background:{{ $dprov['color'] }}22;border:1px solid {{ $dprov['color'] }}44;color:{{ $dprov['color'] }};justify-content:center;border-radius:0.875rem;">
                {{ $dprov['icon'] }}
            </div>
            <div style="flex:1;min-width:0;">
                <h2 style="font-size:1rem;font-weight:800;color:white;word-break:break-all;">{{ $da->email }}</h2>
                <p style="font-size:0.75rem;color:rgba(168,85,247,0.7);margin-top:0.1rem;">{{ $dprov['name'] }}</p>
            </div>
            <button wire:click="closeDrawer" class="ui-btn-ghost" title="Cerrar">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div class="ui-drawer-body">
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Status badges --}}
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <span class="ui-badge {{ $da->is_active ? 'ui-badge--success' : 'ui-badge--error' }}">
                    <span class="ui-badge-dot {{ $da->is_active ? 'ui-badge-dot--success' : 'ui-badge-dot--error' }}" style="animation:none;"></span>
                    {{ $da->is_active ? 'Activa' : 'Inactiva' }}
                </span>
                <span class="ui-badge {{ $da->users->count() > 0 ? 'ui-badge--violet' : 'ui-badge--slate' }}">
                    {{ $da->users->count() > 0 ? '👥 ' . $da->users->count() . ' usuario(s)' : 'Sin asignar' }}
                </span>
            </div>

            {{-- IMAP Config --}}
            <div>
                <div class="ui-sect">
                    <div class="ui-sect-title">Configuración IMAP</div>
                    <div class="ui-sect-line"></div>
                </div>
                <div class="ui-info-grid">
                    @foreach([['Host','imap_host'],['Puerto','imap_port'],['Encriptación','imap_encryption'],['Usuario','username']] as [$lbl,$field])
                    <div class="ui-info-item">
                        <div class="ui-info-key">{{ $lbl }}</div>
                        <div class="ui-info-val">{{ $da->$field ?? '—' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Users assigned --}}
            <div>
                <div class="ui-sect">
                    <div class="ui-sect-title">Usuarios Asignados</div>
                    <div class="ui-sect-line"></div>
                </div>
                @if($da->users->count() > 0)
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    @foreach($da->users as $u)
                    <div style="display:flex;align-items:center;gap:0.875rem;background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);border-radius:var(--ui-radius-sm);padding:0.75rem;">
                        <div class="ui-avatar">
                            {{ strtoupper(substr($u->name,0,1)) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.85rem;color:white;font-weight:600;">{{ $u->name }}</div>
                            <div style="font-size:0.72rem;color:rgba(148,163,184,0.5);word-break:break-all;">{{ $u->email }}</div>
                        </div>
                        <span class="ui-badge ui-badge--info" style="font-size:0.62rem;">{{ $u->role }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="ui-empty ui-card" style="padding:1.5rem;">
                    <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);">Sin usuarios asignados</p>
                    <p style="font-size:0.75rem;color:rgba(100,116,139,0.5);margin-top:0.25rem;">Asigna usuarios al editar la cuenta</p>
                </div>
                @endif
            </div>

            {{-- Last checked --}}
            <div>
                <div class="ui-sect">
                    <div class="ui-sect-title">Última Verificación</div>
                    <div class="ui-sect-line"></div>
                </div>
                <div style="background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);border-radius:var(--ui-radius-sm);padding:0.875rem;display:flex;align-items:center;gap:0.75rem;">
                    <svg width="18" height="18" fill="none" stroke="rgba(168,85,247,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-size:0.875rem;color:rgba(226,232,240,0.6);">{{ $da->last_checked_at ? $da->last_checked_at->diffForHumans() : 'Nunca verificado' }}</span>
                </div>
            </div>

            {{-- Drawer Actions --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;border-top:1px solid rgba(255,255,255,0.06);padding-top:1.25rem;margin-top:auto;">
                <a wire:navigate href="{{ route('admin.email-accounts.edit', $da->id) }}" class="ui-btn ui-btn-secondary" style="width:100%;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Editar / Asignar
                </a>
                <button wire:click="confirmDelete({{ $da->id }})" class="ui-btn ui-btn-danger" style="width:100%;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Eliminar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ══ HERO HEADER ══ --}}
<div class="ui-hero ui-anim-in">
    <div style="position:relative;z-index:1;">
        <div style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.65rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(168,85,247,0.7);background:rgba(168,85,247,0.08);border:1px solid rgba(168,85,247,0.2);border-radius:9999px;padding:0.3rem 0.875rem;margin-bottom:0.875rem;">
            <svg width="10" height="10" fill="#a855f7" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
            Admin Panel
        </div>
        <h1 class="ui-hero-title">Cuentas de Correo</h1>
        <p style="font-size:0.9rem;color:rgba(148,163,184,0.65);margin-top:0.35rem;">Gestiona las bandejas IMAP y sus usuarios asignados</p>
    </div>
    <div style="position:relative;z-index:1;">
        <a wire:navigate href="{{ route('admin.email-accounts.create') }}" class="ui-btn ui-btn-primary">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nueva Cuenta
        </a>
    </div>
</div>

{{-- ══ STATS PILLS ══ --}}
<div class="ui-stats ui-anim-in ui-delay-1">
    <div class="ui-stat ui-stat--all">
        <div class="ui-stat-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
        <div>
            <div class="ui-stat-val">{{ $stats['total'] }}</div>
            <div class="ui-stat-label">Total</div>
        </div>
    </div>
    <div class="ui-stat ui-stat--success">
        <div class="ui-stat-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
        <div>
            <div class="ui-stat-val">{{ $stats['active'] }}</div>
            <div class="ui-stat-label">Activas</div>
        </div>
    </div>
    <div class="ui-stat ui-stat--error">
        <div class="ui-stat-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></div>
        <div>
            <div class="ui-stat-val">{{ $stats['inactive'] }}</div>
            <div class="ui-stat-label">Inactivas</div>
        </div>
    </div>
    <div class="ui-stat ui-stat--info">
        <div class="ui-stat-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
        <div>
            <div class="ui-stat-val">{{ $stats['assigned'] }}</div>
            <div class="ui-stat-label">Asignadas</div>
        </div>
    </div>
    <div class="ui-stat ui-stat--warning">
        <div class="ui-stat-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
        <div>
            <div class="ui-stat-val">{{ $stats['unassigned'] }}</div>
            <div class="ui-stat-label">Sin Asignar</div>
        </div>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div class="ui-card p-4 ui-anim-in ui-delay-2">
    <div class="flex flex-wrap gap-3 items-center">

        {{-- Search --}}
        <div class="ui-search-wrap" style="min-width:220px;flex:1;">
            <div class="ui-search-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="ui-search" placeholder="Buscar cuenta, host o usuario...">
            <div wire:loading wire:target="search" style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);">
                <svg class="animate-spin" width="14" height="14" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </div>
            @if($search)
            <button wire:click="$set('search','')" style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(148,163,184,0.5);cursor:pointer;font-size:1.1rem;padding:0;">&times;</button>
            @endif
        </div>

        {{-- Status filter --}}
        <select wire:model.live="status" class="ui-filter-select">
            <option value="">Todos los estados</option>
            <option value="1">✅ Solo activas</option>
            <option value="0">⏸ Solo inactivas</option>
        </select>

        {{-- Assignment filter --}}
        <select wire:model.live="assigned" class="ui-filter-select">
            <option value="">Asignación</option>
            <option value="1">👥 Con usuarios</option>
            <option value="0">🔓 Sin asignar</option>
        </select>

        {{-- Sort --}}
        <select wire:model.live="sortBy" class="ui-filter-select">
            <option value="email">Ordenar: Correo</option>
            <option value="imap_host">Ordenar: Host</option>
            <option value="created_at">Ordenar: Más nuevas</option>
            <option value="last_checked_at">Ordenar: Verificadas</option>
        </select>

        <button wire:click="sortBy('{{ $sortBy }}')" class="ui-btn-ghost" style="background:rgba(168,85,247,0.1);color:#c4b5fd;border:1px solid rgba(168,85,247,0.2);" title="{{ $sortDir === 'asc' ? 'Ascendente' : 'Descendente' }}">
            @if($sortDir === 'asc')
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
            @endif
        </button>

        {{-- View toggle --}}
        <div class="ui-view-toggle">
            <button wire:click="$set('view','cards')" class="ui-view-btn {{ $view === 'cards' ? 'active' : '' }}" title="Tarjetas">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
            <button wire:click="$set('view','table')" class="ui-view-btn {{ $view === 'table' ? 'active' : '' }}" title="Tabla">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </button>
        </div>

        <span style="font-size:0.75rem;color:rgba(100,116,139,0.6);white-space:nowrap;">{{ $accounts->total() }} cuenta(s)</span>
    </div>
</div>

{{-- ══ CARDS VIEW ══ --}}
@if($view === 'cards')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 ui-anim-in ui-delay-3"
     wire:loading.class="opacity-60" wire:target="search,status,assigned,sortBy">

    @forelse($accounts as $account)
    @php
        $prov = \App\Livewire\Admin\EmailAccountList::detectProvider($account->imap_host ?? '');
    @endphp
    <div wire:key="ea-{{ $account->id }}" class="ui-card ui-card--lift" style="border-top:2px solid {{ $prov['color'] }};">
        
        {{-- Fondo radial glow --}}
        <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:radial-gradient(ellipse at top left, {{ $prov['color'] }}15, transparent 60%);pointer-events:none;"></div>

        {{-- Card Header --}}
        <div style="padding:1.25rem 1.25rem 0.875rem;display:flex;align-items:flex-start;gap:1rem;position:relative;z-index:1;">
            <div class="ui-provider-badge" style="width:2.5rem;height:2.5rem;font-size:{{ strlen($prov['icon']) > 1 ? '0.85rem' : '1.2rem' }};background:{{ $prov['color'] }}22;border:1px solid {{ $prov['color'] }}44;color:{{ $prov['color'] }};justify-content:center;border-radius:0.625rem;flex-shrink:0;">
                {{ $prov['icon'] }}
            </div>
            <div style="flex:1;min-width:0;">
                <h3 style="font-size:0.875rem;font-weight:800;color:white;word-break:break-all;line-height:1.2;">{{ $account->email }}</h3>
                <p style="font-size:0.7rem;color:rgba(100,116,139,0.7);margin-top:0.15rem;font-family:monospace;">{{ $account->imap_host }}:{{ $account->imap_port }}</p>
                <div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.35rem;">
                    <span class="ui-badge ui-badge--violet" style="font-size:0.6rem;padding:0.15rem 0.5rem;">
                        {{ strtoupper($account->imap_encryption ?? 'SSL') }}
                    </span>
                    <span class="ui-badge" style="font-size:0.6rem;padding:0.15rem 0.5rem;background:{{ $prov['color'] }}15;border:1px solid {{ $prov['color'] }}33;color:{{ $prov['color'] }};">
                        {{ $prov['name'] }}
                    </span>
                </div>
            </div>
            {{-- Pulse --}}
            <span class="ui-pulse {{ $account->is_active ? 'ui-pulse--active' : 'ui-pulse--inactive' }}" title="{{ $account->is_active ? 'Activa' : 'Inactiva' }}"></span>
        </div>

        {{-- User chips --}}
        <div style="padding:0 1.25rem 0.875rem;position:relative;z-index:1;">
            @if($account->users->count() > 0)
            <div style="display:flex;flex-wrap:wrap;gap:0.35rem;">
                @foreach($account->users->take(3) as $u)
                <span class="ui-chip">
                    <span class="ui-chip-avatar">{{ strtoupper(substr($u->name,0,1)) }}</span>
                    {{ $u->username ?? $u->name }}
                </span>
                @endforeach
                @if($account->users->count() > 3)
                <span class="ui-chip">+{{ $account->users->count() - 3 }} más</span>
                @endif
            </div>
            @else
            <span class="ui-chip ui-chip--slate">Sin usuarios asignados</span>
            @endif
        </div>

        {{-- Card Mini Stats --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;border-top:1px solid rgba(255,255,255,0.05);border-bottom:1px solid rgba(255,255,255,0.05);position:relative;z-index:1;background:rgba(0,0,0,0.1);">
            <div style="padding:0.65rem;text-align:center;border-right:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:1rem;font-weight:900;color:white;">{{ $account->users->count() }}</div>
                <div style="font-size:0.58rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.6);font-weight:600;">Usuarios</div>
            </div>
            <div style="padding:0.65rem;text-align:center;">
                <div style="font-size:0.65rem;font-weight:600;color:rgba(148,163,184,0.7);">
                    {{ $account->last_checked_at ? $account->last_checked_at->diffForHumans(['short'=>true]) : 'Nunca' }}
                </div>
                <div style="font-size:0.58rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.6);font-weight:600;">Verificado</div>
            </div>
        </div>

        {{-- Card Actions --}}
        <div style="padding:0.875rem 1.25rem;display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1;">
            <label class="ui-toggle-wrap" title="{{ $account->is_active ? 'Desactivar' : 'Activar' }}">
                <input type="checkbox" class="ui-toggle-inp" {{ $account->is_active ? 'checked' : '' }} wire:click="toggleActive({{ $account->id }})" wire:loading.attr="disabled">
                <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
            </label>
            <div style="display:flex;gap:0.4rem;">
                <button wire:click="openDrawer({{ $account->id }})" class="ui-btn-ghost" title="Ver detalles">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
                <a wire:navigate href="{{ route('admin.email-accounts.edit', $account->id) }}" class="ui-btn-ghost" title="Editar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <button wire:click="confirmDelete({{ $account->id }})" class="ui-btn-ghost danger" title="Eliminar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    @empty
    <div class="col-span-full ui-empty ui-card">
        <div class="ui-empty-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="ui-empty-title">Sin resultados</h3>
        <p class="ui-empty-sub">No hay cuentas que coincidan con tu búsqueda.</p>
        <a wire:navigate href="{{ route('admin.email-accounts.create') }}" class="ui-btn ui-btn-primary" style="margin-top:1.5rem;">Agregar Cuenta</a>
    </div>
    @endforelse
</div>

{{-- ══ TABLE VIEW ══ --}}
@else
<div class="ui-card ui-table-wrap ui-anim-in ui-delay-3" wire:loading.class="opacity-60" wire:target="search,status,assigned,sortBy">
    <table class="ui-table">
        <thead>
            <tr>
                <th class="ui-th-sort" wire:click="sortBy('email')">Cuenta @if($sortBy==='email')<span style="color:var(--ui-pink);">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</th>
                <th class="ui-th-sort" wire:click="sortBy('imap_host')">Host @if($sortBy==='imap_host')<span style="color:var(--ui-pink);">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</th>
                <th>Proveedor</th>
                <th>Usuarios</th>
                <th>Estado</th>
                <th class="ui-th-sort" wire:click="sortBy('last_checked_at')">Verificado @if($sortBy==='last_checked_at')<span style="color:var(--ui-pink);">{{ $sortDir==='asc'?'↑':'↓' }}</span>@endif</th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $account)
            @php $prov = \App\Livewire\Admin\EmailAccountList::detectProvider($account->imap_host ?? ''); @endphp
            <tr wire:key="ea-row-{{ $account->id }}">
                <td>
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-provider-badge" style="width:2rem;height:2rem;border-radius:0.5rem;background:{{ $prov['color'] }}22;border:1px solid {{ $prov['color'] }}44;color:{{ $prov['color'] }};font-size:0.75rem;justify-content:center;padding:0;">{{ $prov['icon'] }}</div>
                        <div>
                            <div style="font-weight:700;color:white;font-size:0.8rem;">{{ $account->email }}</div>
                            <div style="font-size:0.65rem;color:rgba(100,116,139,0.6);">{{ $account->username }}</div>
                        </div>
                    </div>
                </td>
                <td><div style="font-size:0.8rem;font-family:monospace;color:rgba(196,181,253,0.8);">{{ $account->imap_host }}:{{ $account->imap_port }}</div></td>
                <td><span class="ui-badge" style="font-size:0.72rem;background:{{ $prov['color'] }}15;border:1px solid {{ $prov['color'] }}33;color:{{ $prov['color'] }};">{{ $prov['name'] }}</span></td>
                <td>
                    @if($account->users->count() > 0)
                    <div style="display:flex;flex-wrap:wrap;gap:0.25rem;">
                        @foreach($account->users->take(2) as $u)
                        <span class="ui-chip">{{ $u->username ?? $u->name }}</span>
                        @endforeach
                        @if($account->users->count() > 2)
                        <span class="ui-chip">+{{ $account->users->count()-2 }}</span>
                        @endif
                    </div>
                    @else
                    <span class="ui-chip ui-chip--slate">Sin asignar</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span class="ui-pulse {{ $account->is_active ? 'ui-pulse--active' : 'ui-pulse--inactive' }}"></span>
                        <span style="font-size:0.72rem;color:{{ $account->is_active ? 'var(--ui-success-2)' : 'var(--ui-error-2)' }};font-weight:600;">{{ $account->is_active ? 'Activa' : 'Inactiva' }}</span>
                    </div>
                </td>
                <td style="font-size:0.75rem;color:rgba(148,163,184,0.5);">{{ $account->last_checked_at ? $account->last_checked_at->diffForHumans(['short'=>true]) : '—' }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem;justify-content:flex-end;">
                        <button wire:click="openDrawer({{ $account->id }})" class="ui-btn-ghost" title="Detalles"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                        <a wire:navigate href="{{ route('admin.email-accounts.edit', $account->id) }}" class="ui-btn-ghost" title="Editar"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></a>
                        <button wire:click="confirmDelete({{ $account->id }})" class="ui-btn-ghost danger" title="Eliminar"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="ui-empty">
                        <div class="ui-empty-icon"><svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                        <h3 class="ui-empty-title">Sin resultados</h3>
                        <p class="ui-empty-sub">No hay cuentas</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- ══ PAGINATION ══ --}}
@if($accounts->hasPages())
<div class="flex justify-center ui-anim-in ui-delay-3 mt-6">
    {{ $accounts->links('pagination::tailwind') }}
</div>
@endif

</div>
