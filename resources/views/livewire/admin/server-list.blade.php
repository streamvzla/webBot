<div class="space-y-6"
     x-data="{ notif: null, drawerOpen: false }"
     @notif.window="notif = $event.detail.message; setTimeout(() => notif = null, 4000)"
     @drawer-open.window="drawerOpen = true"
     @drawer-close.window="drawerOpen = false">

{{-- ══════════════════════════════════════════════════════════════════ 
     SERVERS — UI-* SYSTEM (GOD LEVEL)
══════════════════════════════════════════════════════════════════  --}}

{{-- ══ TOAST ══ --}}
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
        <h3 class="ui-modal-title">Eliminar Servidor</h3>
        <p class="ui-modal-body">¿Estás seguro de que deseas eliminar este servidor IMAP permanentemente? Se perderá toda la configuración. Esta acción no se puede deshacer.</p>
        <div class="ui-modal-footer">
            <button wire:click="cancelDelete" class="ui-btn ui-btn-secondary">Cancelar</button>
            <button wire:click="deleteServer" class="ui-btn ui-btn-danger">
                <span wire:loading.remove wire:target="deleteServer">Sí, eliminar</span>
                <span wire:loading wire:target="deleteServer">Eliminando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ══ DRAWER OVERLAY ══ --}}
@if($drawerServerId)
<div class="ui-drawer-overlay active" wire:click="closeDrawer"></div>
@endif

{{-- ══ DETAIL DRAWER ══ --}}
<div class="ui-drawer {{ $drawerServerId ? 'active' : '' }}">
    @if($drawerServerId && $this->drawerServer)
    @php
        $ds = $this->drawerServer;
        $dprov = \App\Livewire\Admin\ServerList::detectProvider($ds->imap_host ?? '');
    @endphp
    <div class="ui-drawer-header">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div class="ui-provider-badge" style="width:3rem;height:3rem;font-size:1.5rem;background:{{ $dprov['color'] }}22;border:1px solid {{ $dprov['color'] }}44;color:{{ $dprov['color'] }};justify-content:center;border-radius:0.875rem;">
                {{ $dprov['icon'] }}
            </div>
            <div style="flex:1;min-width:0;">
                <h2 style="font-size:1.1rem;font-weight:800;color:white;line-height:1.2;word-break:break-all;">{{ $ds->email }}</h2>
                <p style="font-size:0.75rem;color:rgba(168,85,247,0.7);margin-top:0.15rem;">{{ $dprov['name'] }}</p>
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
                <span class="ui-badge {{ $ds->is_active ? 'ui-badge--success' : 'ui-badge--error' }}">
                    <span class="ui-badge-dot {{ $ds->is_active ? 'ui-badge-dot--success' : 'ui-badge-dot--error' }}" style="animation:none;"></span>
                    {{ $ds->is_active ? 'Activo' : 'Inactivo' }}
                </span>
                <span class="ui-badge {{ $ds->is_authorized ? 'ui-badge--info' : 'ui-badge--warning' }}">
                    {{ $ds->is_authorized ? '✅ Autorizado' : '🔒 Sin Autorizar' }}
                </span>
            </div>

            {{-- Connection info --}}
            <div>
                <div class="ui-sect">
                    <div class="ui-sect-title">Configuración IMAP</div>
                    <div class="ui-sect-line"></div>
                </div>
                <div class="ui-info-grid">
                    @foreach([['Host','imap_host'],['Puerto','imap_port'],['Encriptación','imap_encryption'],['Usuario','username']] as [$lbl,$field])
                    <div class="ui-info-item">
                        <div class="ui-info-key">{{ $lbl }}</div>
                        <div class="ui-info-val">{{ $ds->$field ?? '—' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Owner --}}
            <div>
                <div class="ui-sect">
                    <div class="ui-sect-title">Propietario</div>
                    <div class="ui-sect-line"></div>
                </div>
                <div style="display:flex;align-items:center;gap:0.875rem;background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);border-radius:var(--ui-radius-sm);padding:0.875rem;">
                    <div class="ui-avatar">
                        {{ strtoupper(substr($ds->user->name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:0.875rem;color:white;font-weight:600;">{{ $ds->user->name ?? 'Sistema' }}</div>
                        <div style="font-size:0.75rem;color:rgba(148,163,184,0.5);">{{ $ds->user->email ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Last checked --}}
            <div>
                <div class="ui-sect">
                    <div class="ui-sect-title">Última Verificación</div>
                    <div class="ui-sect-line"></div>
                </div>
                <div style="background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);border-radius:var(--ui-radius-sm);padding:0.875rem;display:flex;align-items:center;gap:0.75rem;">
                    <svg width="18" height="18" fill="none" stroke="rgba(168,85,247,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-size:0.875rem;color:rgba(226,232,240,0.7);">
                        {{ $ds->last_checked_at ? $ds->last_checked_at->diffForHumans() : 'Nunca verificado' }}
                    </span>
                </div>
            </div>

            {{-- Quick test result --}}
            @if($testResult && $testingId === null)
            <div style="{{ $testResult === 'success' ? 'background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);' : 'background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);' }}border-radius:var(--ui-radius-sm);padding:1rem;">
                <div style="font-size:0.8rem;font-weight:700;color:{{ $testResult === 'success' ? '#34d399' : '#f87171' }};margin-bottom:0.35rem;">
                    {{ $testResult === 'success' ? '✅ Conexión Exitosa' : '❌ Error de Conexión' }}
                </div>
                <div style="font-size:0.8rem;color:rgba(148,163,184,0.7);">{{ $testMessage }}</div>
            </div>
            @endif

            {{-- Actions --}}
            <div style="display:flex;flex-direction:column;gap:0.75rem;border-top:1px solid rgba(255,255,255,0.06);padding-top:1.25rem;margin-top:auto;">
                <button wire:click="quickTest({{ $ds->id }})" class="ui-btn ui-btn-primary" style="width:100%;" wire:loading.attr="disabled" wire:target="quickTest">
                    <span wire:loading.remove wire:target="quickTest" style="display:flex;align-items:center;gap:0.5rem;justify-content:center;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Diagnosticar Conexión
                    </span>
                    <span wire:loading wire:target="quickTest" style="display:flex;align-items:center;gap:0.5rem;justify-content:center;">
                        <svg class="animate-spin" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/><path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Verificando...
                    </span>
                </button>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <a wire:navigate href="{{ route('admin.servers.edit', $ds->id) }}" class="ui-btn ui-btn-secondary" style="width:100%;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Editar
                    </a>
                    <button wire:click="confirmDelete({{ $ds->id }})" class="ui-btn ui-btn-danger" style="width:100%;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ══ HERO HEADER (Aislado) ══ --}}
<div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
    <div>
        <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#7c3aed;margin-bottom:0.5rem;">
            Administración Global
        </div>
        <h1 class="ui-hero-title">Servidores IMAP</h1>
        <p class="ui-hero-sub">Gestiona las conexiones a los servidores de correo para la lectura de códigos.</p>
    </div>
</div>

{{-- ══ MÉTRICAS GLASS + ACCIÓN ══ --}}
<div class="ui-anim-in ui-delay-1" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    
    <div class="ae-metric">
        <div class="ae-metric-icon" style="background:rgba(148,163,184,0.08);">
            <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.55)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
        </div>
        <div>
            <div class="ae-metric-num" style="color:white;">{{ $stats['total'] ?? 0 }}</div>
            <div class="ae-metric-label">Total</div>
        </div>
    </div>

    <div class="ae-metric">
        <div class="ae-metric-icon" style="background:rgba(52,211,153,0.08);">
            <svg width="20" height="20" fill="none" stroke="rgba(52,211,153,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <div class="ae-metric-num" style="color:#34d399;">{{ $stats['active'] ?? 0 }}</div>
            <div class="ae-metric-label">Activos</div>
        </div>
    </div>

    <div class="ae-metric">
        <div class="ae-metric-icon" style="background:rgba(248,113,113,0.08);">
            <svg width="20" height="20" fill="none" stroke="rgba(248,113,113,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div>
            <div class="ae-metric-num" style="color:#f87171;">{{ $stats['inactive'] ?? 0 }}</div>
            <div class="ae-metric-label">Inactivos</div>
        </div>
    </div>

    <div class="ae-metric">
        <div class="ae-metric-icon" style="background:rgba(56,189,248,0.08);">
            <svg width="20" height="20" fill="none" stroke="rgba(56,189,248,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
            <div class="ae-metric-num" style="color:#38bdf8;">{{ $stats['authorized'] ?? 0 }}</div>
            <div class="ae-metric-label">Autorizados</div>
        </div>
    </div>

    <div style="display:flex;align-items:center;flex-shrink:0;">
        <a wire:navigate href="{{ route('admin.servers.create') }}" class="ui-btn ui-btn-primary">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo Servidor
        </a>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div class="ae-toolbar ui-anim-in ui-delay-1">

        {{-- Buscar --}}
        <div class="ae-search-wrap" style="min-width:220px;">
            <div class="ae-search-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="ae-search" placeholder="Buscar host, correo o usuario..."/>
            <div wire:loading wire:target="search" style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);">
                <svg class="animate-spin" width="14" height="14" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </div>
            @if($search)
            <button wire:click="$set('search','')" style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(148,163,184,0.5);cursor:pointer;font-size:1.1rem;padding:0;">&times;</button>
            @endif
        </div>

        {{-- Estado --}}
        <select wire:model.live="status" class="ae-select">
            <option value="">Todos los estados</option>
            <option value="1">✅ Solo activos</option>
            <option value="0">✖ Solo inactivos</option>
        </select>

        {{-- Autorización --}}
        <select wire:model.live="authorized" class="ae-select">
            <option value="">Autorización</option>
            <option value="1">✅ Autorizados</option>
            <option value="0">🔒 Sin autorizar</option>
        </select>

        {{-- Ordenar --}}
        <select wire:model.live="sortBy" class="ae-select">
            <option value="email">Ordenar: Correo</option>
            <option value="imap_host">Ordenar: Host</option>
            <option value="created_at">Ordenar: Más nuevos</option>
            <option value="last_checked_at">Ordenar: Verificados</option>
        </select>

        {{-- Dir --}}
        <button wire:click="sortBy('{{ $sortBy }}')" class="ui-btn-ghost" style="background:rgba(168,85,247,0.1);color:#c4b5fd;border:1px solid rgba(168,85,247,0.2);" title="{{ $sortDir === 'asc' ? 'Ascendente' : 'Descendente' }}">
            @if($sortDir === 'asc')
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
            @endif
        </button>

        {{-- Vista --}}
        <div class="ae-view-grp">
            <button wire:click="$set('view','cards')" class="ae-view-btn {{ $view === 'cards' ? 'active' : '' }}" title="Tarjetas">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
            <button wire:click="$set('view','table')" class="ae-view-btn {{ $view === 'table' ? 'active' : '' }}" title="Tabla">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </button>
        </div>

        <span style="font-size:0.75rem;color:rgba(100,116,139,0.6);white-space:nowrap;margin-left:auto;">
            {{ $emailAccounts->total() }} servidor(es)
        </span>
</div>

{{-- ══ VISTA CARDS ══ --}}
@if($view === 'cards')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 ui-anim-in ui-delay-2" style="margin-top:1.5rem;"
     wire:loading.class="opacity-60" wire:target="search,status,authorized,sortBy">

    @forelse($emailAccounts as $account)
    @php
        $prov = \App\Livewire\Admin\ServerList::detectProvider($account->imap_host ?? '');
    @endphp
    <div wire:key="srv-{{ $account->id }}" class="ae-card" style="border-top:2px solid {{ $prov['color'] }};">
        
        {{-- Fondo radial glow --}}
        <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:radial-gradient(ellipse at top left, {{ $prov['color'] }}15, transparent 60%);pointer-events:none;"></div>

        {{-- Header --}}
        <div style="padding:1.25rem 1.25rem 0.875rem;display:flex;align-items:flex-start;gap:1rem;position:relative;z-index:1;">
            <div class="ui-provider-badge" style="width:2.5rem;height:2.5rem;font-size:{{ strlen($prov['icon']) > 1 ? '0.85rem' : '1.2rem' }};background:{{ $prov['color'] }}22;border:1px solid {{ $prov['color'] }}44;color:{{ $prov['color'] }};justify-content:center;border-radius:0.625rem;flex-shrink:0;">
                {{ $prov['icon'] }}
            </div>
            <div style="flex:1;min-width:0;">
                <h3 style="font-size:0.9rem;font-weight:800;color:white;line-height:1.2;word-break:break-all;">{{ $account->email }}</h3>
                <p style="font-size:0.7rem;color:rgba(100,116,139,0.7);margin-top:0.1rem;">{{ $account->imap_host }}:{{ $account->imap_port }}</p>
                
                <div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.35rem;">
                    <span class="ui-badge {{ $account->is_authorized ? 'ui-badge--info' : 'ui-badge--warning' }}" style="font-size:0.6rem;padding:0.15rem 0.5rem;">
                        {{ $account->is_authorized ? '✅ Auth' : '🔒 Pendiente' }}
                    </span>
                    <span class="ui-badge ui-badge--violet" style="font-size:0.6rem;padding:0.15rem 0.5rem;">
                        {{ strtoupper($account->imap_encryption ?? 'SSL') }}
                    </span>
                </div>
            </div>
            {{-- Neon Pulse Status --}}
            <div>
                @if($account->is_active && $account->is_authorized)
                    <span class="ui-pulse ui-pulse--active" title="Activo y Autorizado"></span>
                @elseif($account->is_active && !$account->is_authorized)
                    <span class="ui-pulse ui-pulse--pending" title="Activo pero pendiente de autorización"></span>
                @else
                    <span class="ui-pulse ui-pulse--inactive" title="Inactivo"></span>
                @endif
            </div>
        </div>

        {{-- Stats Bar --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;border-top:1px solid rgba(255,255,255,0.05);border-bottom:1px solid rgba(255,255,255,0.05);position:relative;z-index:1;background:rgba(0,0,0,0.1);">
            <div style="padding:0.65rem;text-align:center;border-right:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:0.65rem;font-weight:600;color:{{ $prov['color'] }};text-transform:uppercase;letter-spacing:0.05em;">{{ $prov['name'] }}</div>
                <div style="font-size:0.58rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.6);font-weight:600;">Proveedor</div>
            </div>
            <div style="padding:0.65rem;text-align:center;">
                <div style="font-size:0.65rem;font-weight:600;color:rgba(148,163,184,0.7);">
                    {{ $account->last_checked_at ? $account->last_checked_at->diffForHumans(['short'=>true]) : 'Nunca' }}
                </div>
                <div style="font-size:0.58rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.6);font-weight:600;">Verificado</div>
            </div>
        </div>

        {{-- Test Result (shown inline if tested) --}}
        @if($testResult && $testingId === null && $drawerServerId === $account->id)
        <div style="padding:0.625rem 1.25rem;position:relative;z-index:1;border-bottom:1px solid rgba(255,255,255,0.04);">
            <span class="ui-badge {{ $testResult === 'success' ? 'ui-badge--success' : 'ui-badge--error' }}">
                {{ $testResult === 'success' ? '✅' : '❌' }} {{ Str::limit($testMessage, 40) }}
            </span>
        </div>
        @endif

        {{-- Actions --}}
        <div style="padding:0.875rem 1.25rem;display:flex;align-items:center;justify-content:space-between;gap:0.5rem;position:relative;z-index:1;">
            {{-- Toggle activo --}}
            <label class="ui-toggle-wrap" title="{{ $account->is_active ? 'Desactivar' : 'Activar' }}">
                <input type="checkbox" class="ui-toggle-inp" {{ $account->is_active ? 'checked' : '' }} wire:click="toggleActive({{ $account->id }})" wire:loading.attr="disabled">
                <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
            </label>

            <div style="display:flex;gap:0.4rem;align-items:center;">
                {{-- Ver detalles / Drawer --}}
                <button wire:click="openDrawer({{ $account->id }})" class="ae-view-btn" title="Ver detalles">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>

                {{-- Quick test --}}
                <button wire:click="quickTest({{ $account->id }})" class="ae-view-btn" style="color:var(--ui-warning);" title="Probar conexión" wire:loading.attr="disabled" wire:target="quickTest">
                    <span wire:loading.remove wire:target="quickTest({{ $account->id }})">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <span wire:loading wire:target="quickTest({{ $account->id }})">
                        <svg width="14" height="14" class="animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>

                {{-- Autorizar (solo admin) --}}
                @if(auth()->user()->id === 1 || auth()->user()->role === 'admin')
                <button wire:click="toggleAuthorization({{ $account->id }})" class="ae-view-btn" style="color:{{ $account->is_authorized ? 'rgba(244,63,94,0.7)' : '#10b981' }};" title="{{ $account->is_authorized ? 'Revocar' : 'Autorizar' }}">
                    @if($account->is_authorized)
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    @else
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    @endif
                </button>
                @endif

                {{-- Editar --}}
                <a wire:navigate href="{{ route('admin.servers.edit', $account->id) }}" class="ae-edit-btn" title="Editar">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Editar
                </a>

                {{-- Eliminar --}}
                <button wire:click="confirmDelete({{ $account->id }})" class="ae-view-btn" style="color:rgba(244,63,94,0.7);" title="Eliminar">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full ui-empty ui-card">
        <div class="ui-empty-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
        </div>
        <h3 class="ui-empty-title">Sin resultados</h3>
        <p class="ui-empty-sub">No se encontraron servidores con esos filtros. Intenta con otra búsqueda o agrega uno nuevo.</p>
        <a wire:navigate href="{{ route('admin.servers.create') }}" class="ui-btn ui-btn-primary" style="margin-top:1.5rem;">Registrar Servidor</a>
    </div>
    @endforelse
</div>

{{-- ══ VISTA TABLA ══ --}}
@else
<div class="ui-card ui-table-wrap ui-anim-in ui-delay-2" wire:loading.class="opacity-60" wire:target="search,status,authorized,sortBy">
    <table class="ui-table">
        <thead>
            <tr>
                <th class="ui-th-sort" wire:click="sortBy('email')">
                    Cuenta
                    @if($sortBy === 'email') <span style="color:var(--ui-pink);">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                </th>
                <th class="ui-th-sort" wire:click="sortBy('imap_host')">
                    Host IMAP
                    @if($sortBy === 'imap_host') <span style="color:var(--ui-pink);">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                </th>
                <th>Proveedor</th>
                <th>Estado</th>
                <th>Autorización</th>
                <th class="ui-th-sort" wire:click="sortBy('last_checked_at')">
                    Verificado
                    @if($sortBy === 'last_checked_at') <span style="color:var(--ui-pink);">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                </th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($emailAccounts as $account)
            @php $prov = \App\Livewire\Admin\ServerList::detectProvider($account->imap_host ?? ''); @endphp
            <tr wire:key="srv-row-{{ $account->id }}">
                <td>
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-provider-badge" style="width:2.25rem;height:2.25rem;border-radius:0.625rem;background:{{ $prov['color'] }}22;border:1px solid {{ $prov['color'] }}44;color:{{ $prov['color'] }};font-size:0.8rem;justify-content:center;padding:0;">
                            {{ $prov['icon'] }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:white;font-size:0.8rem;">{{ $account->email }}</div>
                            <div style="font-size:0.65rem;color:rgba(100,116,139,0.6);">{{ $account->username }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size:0.8rem;font-family:monospace;color:rgba(196,181,253,0.8);">{{ $account->imap_host }}:{{ $account->imap_port }}</div>
                    <div style="font-size:0.65rem;color:rgba(100,116,139,0.5);text-transform:uppercase;margin-top:0.1rem;">{{ $account->imap_encryption ?? 'ssl' }}</div>
                </td>
                <td>
                    <span style="font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:9999px;background:{{ $prov['color'] }}15;border:1px solid {{ $prov['color'] }}33;color:{{ $prov['color'] }};">{{ $prov['name'] }}</span>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        @if($account->is_active && $account->is_authorized)
                            <span class="ui-pulse ui-pulse--active"></span>
                            <span style="font-size:0.72rem;color:var(--ui-success-2);font-weight:600;">Activo</span>
                        @elseif($account->is_active)
                            <span class="ui-pulse ui-pulse--pending"></span>
                            <span style="font-size:0.72rem;color:var(--ui-warning-2);font-weight:600;">Pendiente</span>
                        @else
                            <span class="ui-pulse ui-pulse--inactive"></span>
                            <span style="font-size:0.72rem;color:var(--ui-error-2);font-weight:600;">Inactivo</span>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="ui-badge {{ $account->is_authorized ? 'ui-badge--info' : 'ui-badge--warning' }}">
                        {{ $account->is_authorized ? '✅ Sí' : '🔒 No' }}
                    </span>
                </td>
                <td style="font-size:0.75rem;color:rgba(148,163,184,0.5);">
                    {{ $account->last_checked_at ? $account->last_checked_at->diffForHumans(['short'=>true]) : '—' }}
                </td>
                <td>
                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.5rem;">
                        <button wire:click="openDrawer({{ $account->id }})" class="ae-view-btn" title="Detalles">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>

                        <button wire:click="quickTest({{ $account->id }})" class="ae-view-btn" style="color:var(--ui-warning);" title="Probar" wire:loading.attr="disabled" wire:target="quickTest({{ $account->id }})">
                            <span wire:loading.remove wire:target="quickTest({{ $account->id }})">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </span>
                            <span wire:loading wire:target="quickTest({{ $account->id }})">
                                <svg width="14" height="14" class="animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>

                        @if(auth()->user()->id === 1 || auth()->user()->role === 'admin')
                        <button wire:click="toggleAuthorization({{ $account->id }})" class="ae-view-btn" style="color:{{ $account->is_authorized ? 'rgba(244,63,94,0.7)' : '#10b981' }};" title="{{ $account->is_authorized ? 'Revocar' : 'Autorizar' }}">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $account->is_authorized ? 'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z' : 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' }}"/></svg>
                        </button>
                        @endif

                        <a wire:navigate href="{{ route('admin.servers.edit', $account->id) }}" class="ae-edit-btn" title="Editar">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Editar
                        </a>

                        <button wire:click="confirmDelete({{ $account->id }})" class="ae-view-btn" style="color:rgba(244,63,94,0.7);" title="Eliminar">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="ui-empty">
                        <div class="ui-empty-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                        </div>
                        <h3 class="ui-empty-title">Sin resultados</h3>
                        <p class="ui-empty-sub">No se encontraron servidores con esos filtros.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- ══ PAGINACIÓN ══ --}}
@if($emailAccounts->hasPages())
<div class="ui-anim-in ui-delay-3 flex justify-center mt-6">
    {{ $emailAccounts->links('pagination::tailwind') }}
</div>
@endif

</div>

