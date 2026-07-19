@extends('admin.layouts.app')

@section('title', 'Planes de Franquicia - Panel de Administración')

@section('content')

    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
        <div>
            <div class="ui-hero-tag">
                Administración Comercial
            </div>
            <h1 class="ui-hero-title">Planes de Franquicia</h1>
            <p class="ui-hero-sub">Configura paquetes para limitar la cantidad de clientes y peticiones diarias de tus revendedores.</p>
        </div>
    </div>

    {{-- ── MÉTRICAS GLASS & ACCIONES ── --}}
    <div class="ui-anim-in ui-delay-1" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        
        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(236,72,153,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(236,72,153,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#ec4899;">{{ $plans->count() }}</div>
                <div class="ae-metric-label">Total de Planes</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(16,185,129,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(16,185,129,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#10b981;">{{ $plans->where('is_active', true)->count() }}</div>
                <div class="ae-metric-label">Activos</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;flex-shrink:0;">
            <a href="{{ route('admin.franchise-plans.create') }}" class="ui-btn ui-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Plan
            </a>
        </div>
    </div>

    {{-- ── TABLA DE RESULTADOS ── --}}
    <div class="ui-anim-in ui-delay-2">
        @if($plans->count() > 0)
            <div class="ae-card">
                <div style="overflow-x:auto;">
                    <table class="ae-table">
                        <thead>
                            <tr>
                                <th>Nombre del Plan</th>
                                <th>Límite de Clientes</th>
                                <th>Límite Consultas/Día</th>
                                <th>Precio Ref.</th>
                                <th style="text-align:center;">Estado</th>
                                <th style="text-align:right;">Administrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:0.75rem;">
                                            <div class="ae-monogram">{{ strtoupper(substr($plan->name, 0, 1)) }}</div>
                                            <div style="display:flex;flex-direction:column;">
                                                <span style="font-size:0.95rem;font-weight:800;color:white;letter-spacing:0.02em;">{{ $plan->name }}</span>
                                                <span style="font-size:0.75rem;color:rgba(148,163,184,0.7);">
                                                    {{ is_array($plan->features) ? count($plan->features) : 0 }} características incluidas
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @if($plan->max_clients)
                                            <span style="color:white;font-weight:700;">{{ $plan->max_clients }}</span> <span style="font-size:0.75rem;color:rgba(148,163,184,0.6);">usuarios</span>
                                        @else
                                            <span class="ui-badge ui-badge--nocode">Ilimitado</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div style="display:flex;align-items:center;gap:0.4rem;">
                                            <svg width="14" height="14" fill="none" stroke="rgba(148,163,184,0.6)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            <span style="color:white;font-weight:700;">{{ number_format($plan->max_queries_per_day_per_client) }}</span>
                                            <span style="font-size:0.75rem;color:rgba(148,163,184,0.5);">/día</span>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @if($plan->price > 0)
                                            <span style="color:#10b981;font-weight:800;">${{ number_format($plan->price, 2) }}</span>
                                        @else
                                            <span style="color:rgba(148,163,184,0.5);font-style:italic;">No definido</span>
                                        @endif
                                    </td>
                                    
                                    <td style="text-align:center;">
                                        @if($plan->is_active)
                                            <span class="ui-badge ui-badge--success">Activo</span>
                                        @else
                                            <span class="ui-badge ui-badge--error">Inactivo</span>
                                        @endif
                                    </td>
                                    
                                    <td style="text-align:right;padding-right:1.5rem;">
                                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;">
                                            <a href="{{ route('admin.franchise-plans.edit', $plan) }}" class="ae-edit-btn" title="Editar Plan">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Editar
                                            </a>
                                            
                                            <form action="{{ route('admin.franchise-plans.destroy', $plan) }}" method="POST" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ae-view-btn" style="color:rgba(244,63,94,0.7);" onclick="return confirm('¿Eliminar plan permanentemente?')" title="Eliminar Plan">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="ae-card" style="padding:4rem 2rem;text-align:center;justify-content:center;align-items:center;">
                <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">Sin planes de franquicia</p>
                <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">Crea el primer plan para definir los límites de tus revendedores.</p>
                <div style="margin-top:1.5rem;">
                    <a href="{{ route('admin.franchise-plans.create') }}" class="ui-btn ui-btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo Plan
                    </a>
                </div>
            </div>
        @endif
    </div>

@endsection
