<div>
<style>
/* ── BULK ACTION BAR ── */
.ui-bulk-bar { position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%) translateY(100px);opacity:0;background:rgba(15,20,50,0.95);border:1px solid rgba(168,85,247,0.4);backdrop-filter:blur(12px);border-radius:1.5rem;padding:0.75rem 1.5rem;display:flex;align-items:center;gap:1.5rem;box-shadow:0 10px 40px rgba(168,85,247,0.25);z-index:999;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.ui-bulk-bar.visible { transform:translateX(-50%) translateY(0);opacity:1; }
.ui-bulk-btn { display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.7);transition:all 0.2s;cursor:pointer;border:none; }
.ui-bulk-btn:hover { transform:translateY(-2px);color:white; }
.ui-bulk-btn.emerald:hover { background:rgba(16,185,129,0.2);color:#34d399; }
.ui-bulk-btn.amber:hover   { background:rgba(245,158,11,0.2);color:#fbbf24; }
.ui-bulk-btn.blue:hover    { background:rgba(59,130,246,0.2);color:#60a5fa; }
.ui-bulk-btn.rose:hover    { background:rgba(239,68,68,0.2);color:#f87171; }
</style>

    {{-- BARRA DE ACCIONES MASIVAS FLOTANTE --}}
    <div class="ui-bulk-bar {{ count($selectedIds) > 0 ? 'visible' : '' }}">
        <div style="display:flex;align-items:center;gap:0.75rem;color:white;font-weight:700;font-size:0.9rem;">
            <span style="background:var(--ui-gradient, linear-gradient(135deg,#a855f7,#ec4899));width:1.5rem;height:1.5rem;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:0.75rem;">{{ count($selectedIds) }}</span>
            clientes seleccionados
        </div>
        <div style="width:1px;height:1.5rem;background:rgba(255,255,255,0.1);"></div>
        <div style="display:flex;align-items:center;gap:0.4rem;">
            <button wire:click="activateSelected" onclick="confirm('¿Activar clientes seleccionados?') || event.stopImmediatePropagation()" class="ui-bulk-btn emerald" title="Activar">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </button>
            <button wire:click="deactivateSelected" onclick="confirm('¿Suspender clientes seleccionados?') || event.stopImmediatePropagation()" class="ui-bulk-btn amber" title="Suspender">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <button wire:click="renewSelected" onclick="confirm('¿Añadir +30 días a todos los clientes seleccionados?') || event.stopImmediatePropagation()" class="ui-bulk-btn blue" title="Renovar (+30 Días)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <button wire:click="deleteSelected" onclick="confirm('¿Eliminar definitivamente estos clientes?') || event.stopImmediatePropagation()" class="ui-bulk-btn rose" title="Eliminar">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>

<div class="space-y-6">

    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
        <div>
            <div class="ui-hero-tag">
                Administración de Usuarios
            </div>
            <h1 class="ui-hero-title">Gestión de Clientes</h1>
            <p class="ui-hero-sub">Administra a tus consumidores finales, sus límites y accesos.</p>
        </div>
    </div>

    @php
        $baseQ = \App\Models\Client::when(auth()->user()->role === 'user', function($q) { return $q->where('user_id', auth()->id()); });
        $totalCount = (clone $baseQ)->count();
        $activeCount = (clone $baseQ)->where('is_active', true)->count();
        $suspendedCount = (clone $baseQ)->where('is_active', false)->count();
    @endphp

    {{-- ── MÉTRICAS GLASS & ACCIONES ── --}}
    <div class="ui-anim-in ui-delay-1" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        
        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(59,130,246,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(59,130,246,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#60a5fa;">{{ $totalCount }}</div>
                <div class="ae-metric-label">Total de Clientes</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(16,185,129,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(16,185,129,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#10b981;">{{ $activeCount }}</div>
                <div class="ae-metric-label">Activos</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(239,68,68,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(239,68,68,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#ef4444;">{{ $suspendedCount }}</div>
                <div class="ae-metric-label">Suspendidos</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;flex-shrink:0;">
            <a href="{{ route('admin.clients.create') }}" wire:navigate class="ui-btn ui-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                Registrar Cliente
            </a>
        </div>
    </div>

    {{-- ── TOOLBAR DE BÚSQUEDA Y FILTROS ── --}}
    <div class="ae-toolbar ui-anim-in ui-delay-1">
        <div class="ae-search-wrap" style="flex:1;">
            <div class="ae-search-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="ae-search" placeholder="Buscar por nombre o correo...">
        </div>
        
        <div style="width:1px;height:2rem;background:rgba(255,255,255,0.1);margin:0 0.5rem;"></div>

        <select wire:model.live="status" class="ui-input" style="width:auto;min-width:160px;margin:0;padding-top:0.4rem;padding-bottom:0.4rem;background:rgba(255,255,255,0.02);border-color:rgba(255,255,255,0.1);color:rgba(241,245,249,0.8);font-size:0.85rem;">
            <option value="" style="background:#050510;">Todos los estados</option>
            <option value="1" style="background:#050510;">Solo Activos</option>
            <option value="0" style="background:#050510;">Solo Suspendidos</option>
        </select>
    </div>

    {{-- ── TABLA DE RESULTADOS ── --}}
    <div class="ui-anim-in ui-delay-2" wire:loading.class="opacity-50">
        @if($clients->count() > 0)
            <div class="ae-card">
                <div style="overflow-x:auto;">
                    <table class="ae-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align:center;">
                                    <input type="checkbox" wire:model.live="selectAll" style="accent-color:#a855f7;width:1rem;height:1rem;cursor:pointer;">
                                </th>
                                <th>Identidad del Cliente</th>
                                @if($showParentColumn)
                                <th>Creado Por</th>
                                @endif
                                <th>Límites y Uso Diario</th>
                                <th>Plataformas</th>
                                <th style="text-align:center;">Estado</th>
                                <th style="text-align:right;">Administrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr wire:key="client-{{ $client->id }}" style="{{ in_array($client->id, $selectedIds) ? 'background:rgba(59,130,246,0.05); border-bottom:1px solid rgba(59,130,246,0.1);' : '' }}">
                                    
                                    {{-- CHECKBOX --}}
                                    <td style="text-align:center;">
                                        <input type="checkbox" wire:model.live="selectedIds" value="{{ $client->id }}" style="accent-color:#a855f7;width:1rem;height:1rem;cursor:pointer;">
                                    </td>
                                    
                                    {{-- IDENTIDAD --}}
                                    <td>
                                        <div style="display:flex;align-items:center;gap:1rem;">
                                            <div class="ae-monogram">
                                                {{ strtoupper(substr($client->name, 0, 1)) }}
                                            </div>
                                            <div style="display:flex;flex-direction:column;">
                                                <span style="font-size:0.95rem;font-weight:800;color:white;letter-spacing:0.02em;">{{ $client->name }}</span>
                                                <div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.2rem;">
                                                    <span style="font-size:0.75rem;color:rgba(148,163,184,0.7);font-weight:500;">{{ $client->email }}</span>
                                                    @if($client->last_query_at)
                                                        <span style="color:rgba(255,255,255,0.2);">•</span>
                                                        <span style="font-size:0.65rem;color:#34d399;text-transform:uppercase;font-weight:700;" title="Última consulta">USO: {{ \Carbon\Carbon::parse($client->last_query_at)->diffForHumans(null, true, true) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- CREADO POR --}}
                                    @if($showParentColumn)
                                    <td>
                                        @if($client->user)
                                            <div style="display:flex;flex-direction:column;">
                                                <span style="font-size:0.85rem;font-weight:600;color:rgba(226,232,240,0.9);">{{ $client->user->name }}</span>
                                                <span style="font-size:0.7rem;color:rgba(148,163,184,0.6);text-transform:uppercase;letter-spacing:0.05em;">Revendedor</span>
                                            </div>
                                        @else
                                            <span style="font-size:0.75rem;color:rgba(148,163,184,0.5);font-style:italic;">Administrador Principal</span>
                                        @endif
                                    </td>
                                    @endif
                                    
                                    {{-- LIMITES --}}
                                    <td>
                                        <div style="display:flex;flex-direction:column;gap:0.4rem;">
                                            <div style="display:flex;align-items:center;gap:0.4rem;">
                                                <svg width="12" height="12" fill="none" stroke="#60a5fa" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                <span style="font-size:0.8rem;color:white;font-weight:700;">{{ $client->query_count ?? 0 }} / {{ $client->max_queries_per_day }}</span>
                                                <span style="font-size:0.7rem;color:rgba(148,163,184,0.5);">consultas hoy</span>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:0.4rem;">
                                                <svg width="12" height="12" fill="none" stroke="#c084fc" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span style="font-size:0.8rem;color:white;font-weight:700;">{{ $client->allowed_emails_count ?? 0 }}</span>
                                                <span style="font-size:0.7rem;color:rgba(148,163,184,0.5);">correos asig.</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- PLATAFORMAS --}}
                                    <td>
                                        @if($client->platforms->count() > 0)
                                            <div style="display:flex;flex-wrap:wrap;gap:0.3rem;max-width:220px;">
                                                @foreach($client->platforms->take(3) as $platform)
                                                    <span class="ui-badge-neon" style="color:{{ $platform->color ?? '#a855f7' }};border-color:{{ $platform->color ?? '#a855f7' }}44;background:{{ $platform->color ?? '#a855f7' }}15;font-size:0.65rem;">{{ $platform->name }}</span>
                                                @endforeach
                                                @if($client->platforms->count() > 3)
                                                    <span class="ui-badge-neon" style="color:#c084fc;border-color:rgba(192,132,252,0.2);background:rgba(192,132,252,0.05);font-size:0.65rem;">+{{ $client->platforms->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span style="font-size:0.75rem;color:rgba(148,163,184,0.5);font-style:italic;">Acceso Global</span>
                                        @endif
                                    </td>
                                    
                                    {{-- ESTADO --}}
                                    <td style="text-align:center;">
                                        @if($client->is_active)
                                            <span class="ui-badge-neon success">Activo</span>
                                        @else
                                            <span class="ui-badge-neon error">Suspendido</span>
                                        @endif
                                    </td>
                                    
                                    {{-- ACCIONES --}}
                                    <td style="text-align:right;">
                                        <a href="{{ route('admin.clients.edit', $client) }}" wire:navigate class="ae-edit-btn">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Configurar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div style="padding:1.5rem;border-top:1px solid rgba(255,255,255,0.05);">
                    {{ $clients->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        @else
            {{-- EMPTY STATE PRO --}}
            <div class="ae-card" style="padding:4rem 2rem;text-align:center;justify-content:center;align-items:center;">
                <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">Aún no hay clientes</p>
                <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">
                    Registra a tus consumidores finales para asignarles correos, permisos y límites.
                </p>
                <div style="margin-top:1.5rem;">
                    <a href="{{ route('admin.clients.create') }}" class="ui-btn ui-btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        Registrar Primer Cliente
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</div>