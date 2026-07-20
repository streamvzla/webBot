@extends('admin.layouts.app')

@section('title', (auth()->id() === 1 ? 'Franquicias & Staff' : (auth()->user()->role === 'admin' ? 'Revendedor & Staff' : 'Mi Equipo')) . ' - Panel de Administración')

@php
    $baseQuery = \App\Models\User::when(auth()->id() !== 1, function ($query) {
        return $query->where('parent_id', auth()->id());
    });
    
    $stats = [
        'total' => (clone $baseQuery)->count(),
        'active' => (clone $baseQuery)->where('is_active', 1)->count(),
        'inactive' => (clone $baseQuery)->where('is_active', 0)->count(),
        'admins' => (clone $baseQuery)->where('role', 'admin')->count(),
        'staff' => (clone $baseQuery)->where('role', 'user')->count(),
    ];
@endphp

@section('content')

    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
        <div>
            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#7c3aed;margin-bottom:0.5rem;">
                Control de Acceso
            </div>
            <h1 class="ui-hero-title">
                @if(auth()->id() === 1) Franquicias & Staff @elseif(auth()->user()->role === 'admin') Revendedores & Staff @else Mi Equipo @endif
            </h1>
            <p class="ui-hero-sub">Administra los accesos y permisos de tu equipo de trabajo en la plataforma.</p>
        </div>
    </div>

    {{-- ── MÉTRICAS GLASS ── --}}
    <div class="ui-anim-in" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(148,163,184,0.07);">
                <svg width="16" height="16" fill="none" stroke="rgba(148,163,184,0.55)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:white;">{{ number_format($stats['total']) }}</div>
                <div class="ae-metric-label">Personal Registrado</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(52,211,153,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(52,211,153,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#34d399;">{{ number_format($stats['active']) }}</div>
                <div class="ae-metric-label">Cuentas Activas</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(248,113,113,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(248,113,113,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#f87171;">{{ number_format($stats['inactive']) }}</div>
                <div class="ae-metric-label">Suspendidos</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(192,132,252,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(192,132,252,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#c084fc;">{{ number_format($stats['staff']) }}</div>
                <div class="ae-metric-label">Revendedores / Staff</div>
            </div>
        </div>
        
        <div style="display:flex;align-items:center;flex-shrink:0;">
            <a href="{{ route('admin.users.create') }}" class="ui-btn ui-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nuevo Staff
            </a>
        </div>
    </div>

    {{-- ── TOOLBAR DE FILTROS ── --}}
    <form id="filters-form" method="GET" class="ae-toolbar ui-anim-in ui-delay-1" style="margin-bottom: 1.5rem;">
        <div class="ae-search-wrap">
            <div class="ae-search-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
            <input type="text" name="search" value="{{ request('search') }}" class="ae-search" placeholder="Buscar por nombre, correo o alias..." onkeydown="if(event.key === 'Enter'){this.form.submit();}">
        </div>

        <select name="role" class="ae-select" onchange="document.getElementById('filters-form').submit()">
            <option value="">🚀 Cualquier Nivel</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>👑 Dueño / Admin</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>👔 Revendedor / Staff</option>
        </select>

        <select name="status" class="ae-select" style="min-width:120px;" onchange="document.getElementById('filters-form').submit()">
            <option value="">👁️ Cualquier estado</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>✅ Activos</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>❌ Suspendidos</option>
        </select>
        
        <div class="ae-divider"></div>
        <div class="ae-view-grp">
            <button type="button" onclick="setUsersView('cards')" class="ae-view-btn" id="btn-view-cards" title="Tarjetas">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/></svg>
            </button>
            <button type="button" onclick="setUsersView('table')" class="ae-view-btn" id="btn-view-table" title="Tabla">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/></svg>
            </button>
        </div>

        <button type="submit" style="display:none;"></button>

        @if(request()->anyFilled(['search', 'role', 'status']))
            <a href="{{ route('admin.users.index') }}" class="ae-view-btn" title="Limpiar Filtros" style="color:#f87171; display:flex; align-items:center; justify-content:center; padding:0.2rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        @endif
    </form>

    {{-- ── RESULTADOS ── --}}
    <div class="ui-anim-in ui-delay-2">
        @if($users->count() > 0)
            
            <div id="users-view-wrapper" class="view-wrapper is-table">
                <script>
                    if(localStorage.getItem('users_view_pref') === 'cards') {
                        document.getElementById('users-view-wrapper').className = 'view-wrapper is-cards';
                    }
                </script>
                
                {{-- VISTA CARDS --}}
                <div id="view-cards" class="view-cards">
                @foreach($users as $user)
                <div class="ae-card">
                    
                    <div class="ae-card-head">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ae-monogram">{{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.4rem;">
                            @if($user->is_active)
                                <span class="ui-badge ui-badge--success">Activo</span>
                            @else
                                <span class="ui-badge ui-badge--error">Suspendido</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ae-card-body">
                        <div class="ae-email">{{ $user->name ?: $user->username }}</div>
                        <div style="margin-bottom:0.625rem;">
                            <span style="font-size:0.72rem;color:rgba(148,163,184,0.6);display:block;margin-bottom:0.4rem;">{{ $user->name ? '@' . $user->username : 'Sin nombre' }}</span>
                            @if($user->role === 'admin')
                                <span class="ui-badge ui-badge--warning">Dueño (Admin)</span>
                            @else
                                <span class="ui-badge ui-badge--neutral">Staff / Revendedor</span>
                            @endif
                        </div>
                        <div class="ae-meta-grid">
                            <div class="ae-meta-cell">
                                <div class="ae-meta-key">Último Acceso</div>
                                <div class="ae-meta-val" style="{{ $user->last_login_at ? 'color:white;' : 'color:rgba(148,163,184,0.7);' }}">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                                </div>
                            </div>
                            <div class="ae-meta-cell" style="overflow:hidden;">
                                <div class="ae-meta-key">Correo</div>
                                <div class="ae-meta-val" style="color:rgba(148,163,184,0.7); overflow:hidden; text-overflow:ellipsis;" title="{{ $user->email }}">
                                    {{ $user->email }}
                                </div>
                            </div>
                            @if($user->role === 'admin' && $user->subscription_ends_at)
                            <div class="ae-meta-cell" style="grid-column: span 2; padding-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.05);">
                                <div class="ae-meta-key">Vencimiento Plan</div>
                                @php $days = $user->getDaysUntilExpiration(); @endphp
                                <div class="ae-meta-val" style="color: {{ $user->isSubscriptionExpired() || $days <= 1 ? '#ef4444' : ($days <= 5 ? '#f59e0b' : '#34d399') }}; font-weight:600;">
                                    {{ $user->subscription_ends_at->format('d M Y') }} 
                                    @if($user->isSubscriptionExpired())
                                        (Vencido)
                                    @else
                                        (en {{ $days }} días)
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ae-card-foot">
                        <span style="font-size:0.62rem;color:rgba(148,163,184,0.3);font-variant-numeric:tabular-nums;">#{{ $user->id }}</span>
                        <a href="{{ route('admin.users.edit', $user) }}" class="ae-edit-btn">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Editar
                        </a>
                        
                        @if(auth()->id() === 1 && $user->role === 'admin')
                            <form action="{{ route('admin.users.renew', $user) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="ae-edit-btn" style="color:#34d399;" title="Renovar +30 Días">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    +30D
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- VISTA TABLA --}}
            <div id="view-table" class="view-table">
                <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;overflow:hidden;">
                <div style="overflow-x:auto;">
                    <table class="ae-table" style="width:100%; min-width:800px;">
                        <thead>
                            <tr>
                                <th style="padding-left:1.5rem;">Identidad</th>
                                <th>Permisos</th>
                                <th>Actividad</th>
                                <th style="text-align:center;">Estado</th>
                                <th style="text-align:right;padding-right:1.5rem;">Administrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                      <td style="padding-left:1.5rem;">
                                          <div style="display:flex;align-items:center;gap:0.75rem;">
                                              <div class="ae-tbl-monogram" style="{{ $user->role === 'admin' ? 'background:linear-gradient(135deg,rgba(168,85,247,0.3),rgba(236,72,153,0.15));border-color:rgba(236,72,153,0.3);color:#f472b6;' : 'background:linear-gradient(135deg,rgba(148,163,184,0.3),rgba(100,116,139,0.15));border-color:rgba(148,163,184,0.3);color:#cbd5e1;' }}">
                                                  {{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}
                                              </div>
                                              <div>
                                                  <div style="font-size:0.85rem;font-weight:700;color:white;">{{ $user->name ?: $user->username }}</div>
                                                  <div style="font-size:0.65rem;color:rgba(148,163,184,0.5);margin-top:0.15rem;font-weight:500;">
                                                      {{ '@' . $user->username }} &middot; {{ $user->email }}
                                                  </div>
                                              </div>
                                          </div>
                                      </td>
                                    
                                      <td>
                                          @if($user->role === 'admin')
                                              <span class="ui-badge ui-badge--warning">Dueño (Admin)</span>
                                          @else
                                              <span class="ui-badge ui-badge--neutral">Staff / Revend.</span>
                                          @endif
                                      </td>
                                    
                                      <td>
                                          <span style="font-size:0.75rem;font-weight:500; {{ $user->last_login_at ? 'color:rgba(148,163,184,0.8);' : 'color:rgba(148,163,184,0.4);font-style:italic;' }}">
                                              {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca ha ingresado' }}
                                          </span>
                                      </td>
                                    
                                      <td style="text-align:center;">
                                          <div style="display:flex; align-items:center; justify-content:center; gap:0.4rem;">
                                              @if($user->is_active)
                                                  <span class="ui-badge ui-badge--success">Activo</span>
                                              @else
                                                  <span class="ui-badge ui-badge--error">Suspend.</span>
                                              @endif
                                              
                                              @if($user->role === 'admin' && $user->subscription_ends_at)
                                                  @if($user->isSubscriptionExpired())
                                                      <span class="ui-badge" style="background:rgba(220,38,38,0.15);color:#ef4444;border-color:rgba(220,38,38,0.3);">Vencido</span>
                                                  @else
                                                      @php $days = $user->getDaysUntilExpiration(); @endphp
                                                      <span class="ui-badge" style="background:{{ $days <= 1 ? 'rgba(220,38,38,0.15)' : ($days <= 5 ? 'rgba(245,158,11,0.15)' : 'rgba(52,211,153,0.1)') }}; color:{{ $days <= 1 ? '#ef4444' : ($days <= 5 ? '#f59e0b' : '#34d399') }}; border-color:{{ $days <= 1 ? 'rgba(220,38,38,0.3)' : ($days <= 5 ? 'rgba(245,158,11,0.3)' : 'rgba(52,211,153,0.2)') }};">
                                                          Vence: {{ $days }}d
                                                      </span>
                                                  @endif
                                              @endif
                                          </div>
                                      </td>
                                    
                                      <td style="text-align:right;padding-right:1.5rem;">
                                          <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.4rem;">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="ae-edit-btn" title="Editar Usuario">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Editar
                                            </a>
                                            
                                            @if(auth()->id() === 1 && $user->role === 'admin')
                                                <form action="{{ route('admin.users.renew', $user) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    <button type="submit" class="ae-edit-btn" style="color:#34d399;" title="Renovar +30 Días" onclick="return confirm('¿Renovar suscripción de {{ $user->username }} por 30 días?')">
                                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        +30D
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ae-view-btn" style="color:rgba(244,63,94,0.7);" onclick="return confirm('¿Eliminar al usuario permanentemente?')" title="Eliminar Usuario">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:0.875rem 1.25rem;margin-top:0.5rem;">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @else
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:4rem 2rem;text-align:center;">
                <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">Sin personal registrado</p>
                <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">Ajusta los filtros o registra un nuevo miembro del equipo.</p>
            </div>
        @endif
    </div>

    <script>
        function setUsersView(view) {
            localStorage.setItem('users_view_pref', view);
            document.getElementById('users-view-wrapper').className = 'view-wrapper is-' + view;
            document.getElementById('btn-view-cards').classList.toggle('active', view === 'cards');
            document.getElementById('btn-view-table').classList.toggle('active', view === 'table');
        }
        document.addEventListener('DOMContentLoaded', () => {
            let savedView = localStorage.getItem('users_view_pref') || 'table';
            document.getElementById('users-view-wrapper').className = 'view-wrapper is-' + savedView;
            document.getElementById('btn-view-cards').classList.toggle('active', savedView === 'cards');
            document.getElementById('btn-view-table').classList.toggle('active', savedView === 'table');
        });
    </script>
@endsection