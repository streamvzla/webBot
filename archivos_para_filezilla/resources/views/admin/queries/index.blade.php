@extends('admin.layouts.app')

@section('title', 'Historial de Consultas - Panel de Administración')
@section('header', 'Historial de Consultas')
@section('description', 'Registro de auditoría de todas las búsquedas de códigos realizadas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- HERO HEADER --}}
    <div class="ui-hero ui-anim-in">
        <div>
            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#7c3aed;margin-bottom:0.5rem;">
                Registro de Auditoría
            </div>
            <h1 class="ui-hero-title">Historial de Consultas</h1>
            <p class="ui-hero-sub">Supervisa en tiempo real las búsquedas de códigos realizadas por clientes y staff.</p>
        </div>
    </div>

    {{-- MÉTRICAS Y PURGA --}}
    <div class="ui-anim-in" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;">
        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(168,85,247,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(168,85,247,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:white;">{{ number_format($queries->total()) }}</div>
                <div class="ae-metric-label">Registros Totales</div>
            </div>
        </div>

        <div class="ae-metric" style="opacity: 0.5;">
            <div class="ae-metric-icon" style="background:rgba(16,185,129,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(16,185,129,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#10b981;">--</div>
                <div class="ae-metric-label">Exitosas</div>
            </div>
        </div>

        <div class="ae-metric" style="opacity: 0.5;">
            <div class="ae-metric-icon" style="background:rgba(239,68,68,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(239,68,68,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#ef4444;">--</div>
                <div class="ae-metric-label">Errores</div>
            </div>
        </div>
        
        @if($queries->count() > 0 && auth()->user()->role === 'admin')
        <div style="display:flex;align-items:center;flex-shrink:0;">
            <form action="{{ route('admin.queries.truncate') }}" method="POST" data-confirm="PELIGRO: Se eliminarán TODOS los registros permanentemente. ¿Deseas purgar la base de datos de consultas?" data-confirm-title="🛑 Destrucción Total" data-confirm-btn="Sí, aniquilar registros">
                @csrf
                <button type="submit" class="ae-btn-purge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Purgar Base de Datos
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- TOOLBAR (FILTROS) --}}
    <form method="GET" class="ae-toolbar ui-anim-in ui-delay-1" id="filter-form">
        <div class="ae-search-wrap">
            <div class="ae-search-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="ae-search" placeholder="Buscar correo, IP o cliente...">
        </div>

        <select name="platform_id" class="ae-select">
            <option value="">Todas las Plataformas</option>
            @foreach(\App\Models\Platform::where('is_active', true)->get() as $platform)
                <option value="{{ $platform->id }}" {{ request('platform_id') == $platform->id ? 'selected' : '' }}>
                    {{ $platform->name }}
                </option>
            @endforeach
        </select>

        <select name="result" class="ae-select">
            <option value="">Cualquier resultado</option>
            <option value="success" {{ request('result') === 'success' ? 'selected' : '' }}>Éxito</option>
            <option value="pending" {{ request('result') === 'pending' ? 'selected' : '' }}>Pendiente</option>
            <option value="no_code" {{ request('result') === 'no_code' ? 'selected' : '' }}>Sin código</option>
            <option value="error" {{ request('result') === 'error' ? 'selected' : '' }}>Error IMAP</option>
        </select>

        <div class="ae-divider"></div>

        <button type="submit" class="ae-btn-save">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filtrar
        </button>

        @if(request()->anyFilled(['search', 'platform_id', 'result']))
            <a href="{{ route('admin.queries.index') }}" class="ae-btn-cancel">Limpiar</a>
        @endif
    </form>

    {{-- TABLA --}}
    <div class="ui-anim-in ui-delay-2">
        @if($queries->count() > 0)
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:0.875rem;overflow-x:auto;">
                <table class="ae-table">
                    <thead>
                        <tr>
                            <th>Fecha & Hora</th>
                            <th>Actor (Origen)</th>
                            <th>Objetivo (Plataforma / Email)</th>
                            <th style="text-align:center;">Estado Búsqueda</th>
                            <th style="text-align:center;">Extracción (Código)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($queries as $query)
                            <tr>
                                <td>
                                    <div style="display:flex;flex-direction:column;gap:0.1rem;">
                                        <span style="font-size:0.85rem;font-weight:700;color:white;">{{ $query->created_at->format('d M, Y') }}</span>
                                        <span style="font-size:0.75rem;font-weight:600;color:rgba(148,163,184,0.6);">{{ $query->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.75rem;">
                                        @if($query->user)
                                            <div class="ae-tbl-monogram" style="color:#e9d5ff;background:rgba(168,85,247,0.1);border-color:rgba(168,85,247,0.2);">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            </div>
                                            <div style="display:flex;flex-direction:column;">
                                                <span style="font-size:0.85rem;font-weight:700;color:#e9d5ff;">{{ $query->user->name ?? $query->user->username }}</span>
                                                <span style="font-size:0.7rem;color:rgba(148,163,184,0.5);">IP: {{ $query->ip_address }}</span>
                                            </div>
                                        @elseif($query->client)
                                            <div class="ae-tbl-monogram" style="color:#bae6fd;background:rgba(56,189,248,0.1);border-color:rgba(56,189,248,0.2);">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                            <div style="display:flex;flex-direction:column;">
                                                <span style="font-size:0.85rem;font-weight:700;color:#bae6fd;">{{ $query->client->name }}</span>
                                                <span style="font-size:0.7rem;color:rgba(148,163,184,0.5);">IP: {{ $query->ip_address }}</span>
                                            </div>
                                        @else
                                            <div class="ae-tbl-monogram">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                            </div>
                                            <div style="display:flex;flex-direction:column;">
                                                <span style="font-size:0.85rem;font-weight:700;color:rgba(241,245,249,0.8);">Anónimo</span>
                                                <span style="font-size:0.7rem;color:rgba(148,163,184,0.5);">IP: {{ $query->ip_address }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <td>
                                    <div style="display:flex;flex-direction:column;gap:0.3rem;">
                                        <div style="display:inline-flex;align-items:center;gap:0.4rem;width:max-content;background:rgba(255,255,255,0.05);padding:0.15rem 0.5rem;border-radius:0.4rem;border:1px solid rgba(255,255,255,0.05);">
                                            @if(isset($query->platform->logo))
                                                <img src="{{ asset('storage/' . $query->platform->logo) }}" style="width:1rem;height:1rem;border-radius:0.2rem;object-fit:cover;" alt="">
                                            @endif
                                            <span style="font-size:0.68rem;font-weight:700;text-transform:uppercase;color:#f472b6;">
                                                {{ $query->platform->name ?? 'DESCONOCIDA' }}
                                            </span>
                                        </div>
                                        <span style="font-size:0.85rem;font-weight:600;color:white;">{{ $query->email }}</span>
                                    </div>
                                </td>
                                
                                <td style="text-align:center;">
                                    @if($query->result === 'success')
                                        <span class="ui-badge-neon success"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg> Exitosa</span>
                                    @elseif($query->result === 'pending')
                                        <span class="ui-badge-neon pending"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" class="animate-spin" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Buscando</span>
                                    @elseif($query->result === 'no_code')
                                        <span class="ui-badge-neon nocode"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg> Sin Código</span>
                                    @elseif($query->result === 'error')
                                        <span class="ui-badge-neon error"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Error IMAP</span>
                                    @else
                                        <span style="font-size:0.7rem;font-weight:700;color:rgba(148,163,184,0.5);">DESCONOCIDO</span>
                                    @endif
                                </td>
                                
                                <td style="text-align:center;">
                                    @if($query->code_status === 'found')
                                        <span class="code-stat-found">Capturado</span>
                                    @elseif($query->code_status === 'not_found')
                                        <span class="code-stat-notfound">Ausente</span>
                                    @elseif($query->code_status === 'error')
                                        <span class="code-stat-error">Fallido</span>
                                    @else
                                        <span style="font-size:0.85rem;color:rgba(148,163,184,0.2);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="padding-top:1.5rem;">
                {{ $queries->appends(request()->query())->links() }}
            </div>
        @else
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:4rem 2rem;text-align:center;">
                <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">El radar está despejado</p>
                <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">No hay rastro de auditoría. Es posible que el historial haya sido purgado o que tus filtros sean demasiado estrictos.</p>
            </div>
        @endif
    </div>

</div>
@endsection
