<div>
    @section('title', 'Gestor de Licencias - Panel de Administración')

    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
        <div>
            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#7c3aed;margin-bottom:0.5rem;">
                Seguridad y Acceso
            </div>
            <h1 class="ui-hero-title">Gestor de Licencias</h1>
            <p class="ui-hero-sub">Administra y emite firmas digitales vitalicias para Codebot.</p>
        </div>
    </div>

    {{-- ── MÉTRICAS Y ACCIONES ── --}}
    <div class="ui-anim-in" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        
        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(148,163,184,0.07);">
                <svg width="16" height="16" fill="none" stroke="rgba(148,163,184,0.55)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:white;">{{ number_format($metrics['total'] ?? 0) }}</div>
                <div class="ae-metric-label">Total Licencias</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(52,211,153,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(52,211,153,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#34d399;">{{ number_format($metrics['active'] ?? 0) }}</div>
                <div class="ae-metric-label">Activas</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(245,158,11,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(245,158,11,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#fbbf24;">{{ number_format($metrics['suspended'] ?? 0) }}</div>
                <div class="ae-metric-label">Suspendidas</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(248,113,113,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(248,113,113,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#f87171;">{{ number_format($metrics['revoked'] ?? 0) }}</div>
                <div class="ae-metric-label">Revocadas</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;flex-shrink:0;">
            <a href="{{ route('admin.licenses.create') }}" wire:navigate class="ui-btn ui-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Emitir Nueva Licencia
            </a>
        </div>
    </div>

    {{-- ── TOOLBAR DE FILTROS ── --}}
    <div class="ae-toolbar ui-anim-in ui-delay-1" style="margin-bottom: 1.5rem;">
        <div class="ae-search-wrap">
            <div class="ae-search-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
            <input type="text" wire:model.live.debounce.300ms="search" class="ae-search" placeholder="Buscar clave o dominio...">
        </div>

        <select wire:model.live="statusFilter" class="ae-select" style="min-width:120px;">
            <option value="">👁️ Cualquier estado</option>
            <option value="active">✅ Activas</option>
            <option value="suspended">⚠️ Suspendidas</option>
            <option value="revoked">❌ Revocadas</option>
        </select>

        @if($search || $statusFilter)
            <button wire:click="$set('search', ''); $set('statusFilter', '')" class="ae-view-btn" title="Limpiar Filtros" style="color:#f87171; display:flex; align-items:center; justify-content:center; padding:0.2rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>

    {{-- ── RESULTADOS (TABLA) ── --}}
    <div class="ui-anim-in ui-delay-2">
        @if($licenses->count() > 0)
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;overflow:hidden;">
                <div style="overflow-x:auto;">
                    <table class="ae-table" style="width:100%; min-width:800px;">
                        <thead>
                            <tr>
                                <th style="padding-left:1.5rem;">Clave de Licencia</th>
                                <th>Dominio Vinculado</th>
                                <th>Cliente</th>
                                <th style="text-align:center;">Estado</th>
                                <th>Activación</th>
                                <th>Último Ping</th>
                                <th style="text-align:right;padding-right:1.5rem;">Administrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licenses as $license)
                                <tr>
                                    <td style="padding-left:1.5rem;">
                                        <div style="font-weight:800;color:#c084fc;font-size:0.9rem;font-family:monospace;letter-spacing:1px;">
                                            {{ $license->license_key }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($license->domain)
                                            <span class="ui-badge ui-badge--neutral" style="text-transform:none;">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                                {{ $license->domain }}
                                            </span>
                                        @else
                                            <span style="font-size:0.8rem;color:rgba(148,163,184,0.4);font-style:italic;">Sin vincular</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex;flex-direction:column;">
                                            <span style="font-size:0.95rem;font-weight:800;color:white;letter-spacing:0.02em;">{{ $license->client_name ?: 'N/A' }}</span>
                                            <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.2rem;">
                                                <span style="font-size:0.75rem;color:rgba(148,163,184,0.7);">{{ $license->client_email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        @if($license->status === 'active')
                                            <span class="ui-badge ui-badge--success">Activa</span>
                                        @elseif($license->status === 'suspended')
                                            <span class="ui-badge ui-badge--warning">Suspendida</span>
                                        @else
                                            <span class="ui-badge ui-badge--error">Revocada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($license->activated_at)
                                            <div style="color:#34d399;font-weight:600;font-size:0.85rem;">{{ $license->activated_at->format('d M, Y') }}</div>
                                        @else
                                            <span style="font-size:0.8rem;color:rgba(148,163,184,0.4);font-style:italic;">No activada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:0.4rem;">
                                            <svg width="14" height="14" fill="none" stroke="rgba(148,163,184,0.6)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span style="font-size:0.85rem;font-weight:500; {{ $license->last_verified_at ? 'color:rgba(226,232,240,0.9);' : 'color:rgba(148,163,184,0.5);font-style:italic;' }}">
                                                {{ $license->last_verified_at ? $license->last_verified_at->diffForHumans() : 'Nunca ha verificado' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td style="text-align:right;padding-right:1.5rem;">
                                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;">
                                            <a href="{{ route('admin.licenses.edit', $license) }}" wire:navigate class="ae-edit-btn" title="Editar Licencia">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Editar
                                            </a>
                                            
                                            <button wire:click="deleteLicense({{ $license->id }})" class="ae-view-btn" style="color:rgba(244,63,94,0.7);" onclick="confirm('¿Estás seguro de eliminar esta licencia permanentemente?') || event.stopImmediatePropagation()" title="Eliminar Licencia">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($licenses->hasPages())
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:0.875rem 1.25rem;margin-top:0.5rem;">
                {{ $licenses->links() }}
            </div>
            @endif
        @else
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:4rem 2rem;text-align:center;">
                <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">Sin licencias registradas</p>
                <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">Genera tu primera licencia para empezar a vender Codebot.</p>
            </div>
        @endif
    </div>

</div>
