<div class="space-y-6"
     x-data="{ notif: null }"
     @notif.window="notif = $event.detail.message; setTimeout(() => notif = null, 4000)">

    {{-- TOAST --}}
    <div x-show="notif"
         class="ui-toast"
         :class="{ 'show': notif }"
         style="display:none; position:fixed; bottom:20px; right:20px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:#34d399; padding:0.75rem 1.5rem; border-radius:0.75rem; z-index:9999; display:flex; align-items:center; gap:0.5rem; backdrop-filter:blur(8px);">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span x-text="notif" style="font-weight:700; font-size:0.9rem;"></span>
    </div>



    {{-- ── MÉTRICAS GLASS ── --}}
    <div class="ui-anim-in" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(148,163,184,0.07);">
                <svg width="16" height="16" fill="none" stroke="rgba(148,163,184,0.55)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:white;">{{ $this->stats['total'] }}</div>
                <div class="ae-metric-label">Total Plataformas</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(52,211,153,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(52,211,153,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#34d399;">{{ $this->stats['active'] }}</div>
                <div class="ae-metric-label">Activas</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(248,113,113,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(248,113,113,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#f87171;">{{ $this->stats['inactive'] }}</div>
                <div class="ae-metric-label">Inactivas</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(56,189,248,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(56,189,248,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#38bdf8;">{{ $this->stats['public'] }}</div>
                <div class="ae-metric-label">Públicas</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;flex-shrink:0;">
            <a href="{{ route('admin.platforms.create') }}" wire:navigate class="ui-btn ui-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva Plataforma
            </a>
        </div>
    </div>

    {{-- ── TOOLBAR DE FILTROS ── --}}
    <div class="ae-toolbar ui-anim-in ui-delay-1">
        <div class="ae-search-wrap">
            <div class="ae-search-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
            <input wire:model.live.debounce.300ms="search" type="text" class="ae-search" placeholder="Buscar plataforma, slug...">
        </div>

        <select wire:model.live="status" class="ae-select">
            <option value="">👁️ Cualquier estado</option>
            <option value="1">✅ Activas</option>
            <option value="0">❌ Inactivas</option>
        </select>

        <select wire:model.live="sortBy" class="ae-select" style="min-width:140px;">
            <option value="name">Ordenar: Nombre</option>
            <option value="created_at">Ordenar: Más nuevas</option>
            <option value="queries_count">Ordenar: Más consultadas</option>
            <option value="clients_count">Ordenar: Más clientes</option>
        </select>

        <button wire:click="toggleSortDir" class="ae-view-btn" title="{{ $sortDir === 'asc' ? 'Ascendente' : 'Descendente' }}">
            @if($sortDir === 'asc')
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
            @else
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
            @endif
        </button>

        <div class="ae-divider"></div>
        <div class="ae-view-grp">
            <button wire:click="$set('view','cards')" class="ae-view-btn {{ $view === 'cards' ? 'active' : '' }}" title="Tarjetas">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/></svg>
            </button>
            <button wire:click="$set('view','table')" class="ae-view-btn {{ $view === 'table' ? 'active' : '' }}" title="Tabla">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/></svg>
            </button>
        </div>
    </div>

    {{-- ── RESULTADOS ── --}}
    <div class="ui-anim-in ui-delay-2" wire:loading.class="opacity-60" wire:target="search,status,sortBy,sortDir,view">
        @if($this->platforms->count() > 0)

            @if($view === 'cards')
                {{-- VISTA CARDS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($this->platforms as $platform)
                        @php $color = $platform->color ?? '#a855f7'; @endphp
                        <div wire:key="plat-{{ $platform->id }}" class="ae-card" style="border-top:2px solid {{ $color }};">
                            
                            <div class="ae-card-head" style="align-items:flex-start; position:relative; overflow:hidden;">
                                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:radial-gradient(ellipse at top left, {{ $color }}15, transparent 70%);pointer-events:none;"></div>
                                <div style="display:flex;align-items:flex-start;gap:1rem;width:100%;z-index:1;">
                                    <div style="width:3rem;height:3rem;border-radius:0.75rem;background:{{ $color }}15;border:1px solid {{ $color }}33;color:{{ $color }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        @if($platform->logo)
                                            <img src="{{ asset(str_starts_with($platform->logo,'platforms_logos') ? $platform->logo : 'storage/'.$platform->logo) }}" alt="{{ $platform->name }}" style="width:100%;height:100%;object-fit:contain;border-radius:0.75rem;">
                                        @else
                                            <span style="font-size:1.4rem;font-weight:900;">{{ strtoupper(substr($platform->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div class="ae-email">{{ $platform->name }}</div>
                                        <div style="font-size:0.72rem;color:rgba(148,163,184,0.6);font-family:monospace;margin-bottom:0.4rem;">{{ $platform->slug }}</div>
                                        <div style="display:flex;gap:0.4rem;">
                                            @if($platform->is_active)
                                                <span class="ui-badge ui-badge--success">Activa</span>
                                            @else
                                                <span class="ui-badge ui-badge--error">Inactiva</span>
                                            @endif
                                            @if($platform->is_public)
                                                <span class="ui-badge ui-badge--info">Pública</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ae-card-body" style="padding:0;">
                                <div style="display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid rgba(255,255,255,0.05);background:rgba(0,0,0,0.1);">
                                    <div style="padding:0.75rem;text-align:center;border-right:1px solid rgba(255,255,255,0.05);">
                                        <div style="font-size:1.1rem;font-weight:900;color:{{ $color }};">{{ number_format($platform->queries_count) }}</div>
                                        <div style="font-size:0.55rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.6);font-weight:700;">Consultas</div>
                                    </div>
                                    <div style="padding:0.75rem;text-align:center;border-right:1px solid rgba(255,255,255,0.05);">
                                        <div style="font-size:1.1rem;font-weight:900;color:#10b981;">{{ $platform->clients_count }}</div>
                                        <div style="font-size:0.55rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.6);font-weight:700;">Clientes</div>
                                    </div>
                                    <div style="padding:0.75rem;text-align:center;">
                                        <div style="font-size:1.1rem;font-weight:900;color:#a855f7;">{{ $platform->subjects_count }}</div>
                                        <div style="font-size:0.55rem;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.6);font-weight:700;">Reglas</div>
                                    </div>
                                </div>

                                @if($platform->subjects_count > 0)
                                    <div style="padding:0.875rem 1.1rem;">
                                        <div style="display:flex;flex-wrap:wrap;gap:0.35rem;">
                                            @foreach($platform->subjects()->take(3)->get() as $sub)
                                                <span style="background:rgba(168,85,247,0.1);color:#c4b5fd;font-size:0.65rem;font-weight:600;padding:0.2rem 0.5rem;border-radius:0.4rem;border:1px solid rgba(168,85,247,0.2);">
                                                    @if($sub->pattern)⚙️@endif {{ Str::limit($sub->subject, 20) }}
                                                </span>
                                            @endforeach
                                            @if($platform->subjects_count > 3)
                                                <span style="color:rgba(148,163,184,0.6);font-size:0.65rem;font-weight:700;">+{{ $platform->subjects_count - 3 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="ae-card-foot" style="justify-content:flex-end; gap:0.5rem;">
                                <button wire:click="toggleActive({{ $platform->id }})" wire:loading.attr="disabled" class="ae-view-btn" style="color:{{ $platform->is_active ? 'rgba(148,163,184,0.5)' : '#10b981' }};" title="{{ $platform->is_active ? 'Desactivar' : 'Activar' }}">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button wire:click="duplicatePlatform({{ $platform->id }})" wire:loading.attr="disabled" class="ae-view-btn" title="Duplicar">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                                <a href="{{ route('admin.platforms.edit', $platform) }}" wire:navigate class="ae-edit-btn">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </a>
                                <button wire:click="deletePlatform({{ $platform->id }})" wire:loading.attr="disabled" wire:confirm="¿Eliminar la plataforma «{{ $platform->name }}»?" class="ae-view-btn" style="color:rgba(244,63,94,0.7);" title="Eliminar">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                {{-- VISTA TABLA --}}
                <div class="view-wrapper is-table">
                    <div class="view-table-box">
                        <table class="ae-table">
                            <thead>
                                <tr>
                                    <th wire:click="sortBy('name')" style="cursor:pointer;user-select:none;">
                                        Plataforma @if($sortBy === 'name') <span style="color:#a855f7;">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                    </th>
                                    <th wire:click="sortBy('queries_count')" style="cursor:pointer;user-select:none;text-align:center;">
                                        Consultas @if($sortBy === 'queries_count') <span style="color:#a855f7;">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                    </th>
                                    <th wire:click="sortBy('clients_count')" style="cursor:pointer;user-select:none;text-align:center;">
                                        Clientes @if($sortBy === 'clients_count') <span style="color:#a855f7;">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span> @endif
                                    </th>
                                    <th style="text-align:center;">Reglas</th>
                                    <th style="text-align:center;">Estado</th>
                                    <th style="text-align:right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->platforms as $platform)
                                    @php $color = $platform->color ?? '#a855f7'; @endphp
                                    <tr wire:key="row-{{ $platform->id }}">
                                        <td>
                                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                                <div style="width:2.25rem;height:2.25rem;border-radius:0.5rem;background:{{ $color }}15;border:1px solid {{ $color }}33;color:{{ $color }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    @if($platform->logo)
                                                        <img src="{{ asset(str_starts_with($platform->logo,'platforms_logos') ? $platform->logo : 'storage/'.$platform->logo) }}" alt="{{ $platform->name }}" style="width:100%;height:100%;object-fit:contain;border-radius:0.5rem;">
                                                    @else
                                                        <span style="font-weight:900;font-size:0.95rem;">{{ strtoupper(substr($platform->name,0,1)) }}</span>
                                                    @endif
                                                </div>
                                                <div style="display:flex;flex-direction:column;">
                                                    <div style="display:flex;align-items:center;gap:0.4rem;">
                                                        <span style="font-size:0.95rem;font-weight:800;color:white;letter-spacing:0.02em;">{{ $platform->name }}</span>
                                                        @if($platform->is_public)<span class="ui-badge ui-badge--info" style="font-size:0.5rem;padding:0.15rem 0.4rem;">Pública</span>@endif
                                                    </div>
                                                    <span style="font-size:0.7rem;color:rgba(148,163,184,0.6);font-family:monospace;">{{ $platform->slug }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td style="text-align:center;font-weight:800;color:{{ $color }};">{{ number_format($platform->queries_count) }}</td>
                                        <td style="text-align:center;font-weight:800;color:#10b981;">{{ $platform->clients_count }}</td>
                                        <td style="text-align:center;font-weight:800;color:#a855f7;">{{ $platform->subjects_count }}</td>
                                        
                                        <td style="text-align:center;">
                                            @if($platform->is_active)
                                                <span class="ui-badge ui-badge--success" style="cursor:pointer;" wire:click="toggleActive({{ $platform->id }})" title="Clic para desactivar">Activa</span>
                                            @else
                                                <span class="ui-badge ui-badge--error" style="cursor:pointer;" wire:click="toggleActive({{ $platform->id }})" title="Clic para activar">Inactiva</span>
                                            @endif
                                        </td>
                                        
                                        <td style="text-align:right;padding-right:1rem;">
                                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.5rem;">
                                                <button wire:click="duplicatePlatform({{ $platform->id }})" wire:loading.attr="disabled" class="ae-view-btn" title="Duplicar">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                </button>
                                                <a href="{{ route('admin.platforms.edit', $platform) }}" class="ae-edit-btn" title="Editar">
                                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Editar
                                                </a>
                                                <button wire:click="deletePlatform({{ $platform->id }})" wire:loading.attr="disabled" wire:confirm="¿Eliminar la plataforma «{{ $platform->name }}»?" class="ae-view-btn" style="color:rgba(244,63,94,0.7);" title="Eliminar">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @else
            <div class="ae-card" style="padding:4rem 2rem;text-align:center;justify-content:center;align-items:center;">
                <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">No se encontraron plataformas</p>
                <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">
                    @if($search) Prueba con otro término de búsqueda. @else Empieza creando tu primera plataforma. @endif
                </p>
                @if(!$search)
                    <div style="margin-top:1.5rem;">
                        <a href="{{ route('admin.platforms.create') }}" class="ui-btn ui-btn-primary">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Nueva Plataforma
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- ── PAGINACIÓN ── --}}
    @if($this->platforms->hasPages())
        <div class="ui-anim-in ui-delay-3" style="display:flex;justify-content:center;margin-top:1.5rem;">
            {{ $this->platforms->links('livewire::tailwind') }}
        </div>
    @endif

</div>
