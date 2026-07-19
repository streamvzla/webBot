@extends('admin.layouts.app')

@section('title', 'Anti-Spam (IPs Bloqueadas) - Panel de Administración')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">
    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in">
        <div>
            <div style="font-size:0.68rem;font-weight:800;letter-spacing:0.15em;text-transform:uppercase;color:var(--ui-primary-1);margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Módulo de Seguridad
            </div>
            <h1 style="font-size:2.25rem;font-weight:900;color:white;letter-spacing:-0.03em;line-height:1.1;margin-bottom:0.75rem;">
                Escudo Anti-Spam
            </h1>
            <p style="font-size:1.05rem;color:rgba(148,163,184,0.8);max-width:35rem;line-height:1.6;">
                Gestiona las direcciones IP que han sido bloqueadas automáticamente por el sistema de seguridad para proteger tu plataforma.
            </p>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="ae-toolbar ui-anim-in ui-delay-1" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div style="display:flex;align-items:center;gap:0.5rem;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:rgba(148,163,184,0.8);"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span style="font-size:0.9rem;font-weight:600;color:white;">Registro de Bloqueos ({{ $bans->total() }})</span>
        </div>
    </div>

    {{-- CONTENIDO (TABLA O EMPTY STATE) --}}
    <div class="ui-anim-in ui-delay-2">
        @if($bans->count() > 0)
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:0.875rem;overflow-x:auto;">
                <table class="ae-table" style="width:100%; min-width:800px;">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem;">Dirección IP</th>
                            <th>Cliente Afectado</th>
                            <th>Motivo</th>
                            <th>Vencimiento</th>
                            <th style="text-align:right;padding-right:1.5rem;">Administrar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bans as $ban)
                            <tr>
                                <td style="padding-left:1.5rem;">
                                    <div style="display:flex;align-items:center;gap:1rem;">
                                        <div style="width:2.5rem;height:2.5rem;border-radius:0.75rem;background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center;border:1px solid rgba(239,68,68,0.2);flex-shrink:0;">
                                            <svg style="width:1.25rem;height:1.25rem;color:#f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p style="font-weight:700;color:white;font-size:1rem;font-family:monospace;letter-spacing:0.05em;">{{ $ban->ip_address }}</p>
                                            <p style="font-size:0.75rem;color:rgba(148,163,184,0.7);margin-top:0.1rem;">
                                                Bloqueada {{ $ban->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($ban->client)
                                        <span style="font-size:0.875rem;font-weight:600;color:rgba(226,232,240,0.9);">{{ $ban->client->name }}</span>
                                        <p style="font-size:0.75rem;color:rgba(148,163,184,0.6);">{{ $ban->client->email }}</p>
                                    @else
                                        <span style="font-size:0.85rem;color:rgba(148,163,184,0.5);font-style:italic;background:rgba(255,255,255,0.03);padding:0.2rem 0.5rem;border-radius:0.25rem;">Desconocido</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size:0.85rem;color:rgba(226,232,240,0.8);background:rgba(255,255,255,0.04);padding:0.3rem 0.6rem;border-radius:0.5rem;">
                                        {{ $ban->reason ?? 'Comportamiento sospechoso' }}
                                    </span>
                                </td>
                                <td>
                                    @if($ban->expires_at)
                                        <span class="ui-badge ui-badge--warning" style="font-size:0.7rem;padding:0.3rem 0.6rem;">
                                            Vence {{ $ban->expires_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="ui-badge ui-badge--error" style="font-size:0.7rem;padding:0.3rem 0.6rem;">
                                            Permanente
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right;padding-right:1.5rem;">
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;">
                                        <form action="{{ route('admin.ip-bans.destroy', $ban) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ae-view-btn" style="color:rgba(52,211,153,0.9);background:rgba(52,211,153,0.1);" onclick="confirm('¿Seguro que deseas DESBANEAR esta IP?') || event.stopImmediatePropagation()" title="Desbanear IP">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                                Desbanear
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="padding-top:1.5rem;">
                {{ $bans->links() }}
            </div>
        @else
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:4rem 2rem;text-align:center;">
                <div style="width:4rem;height:4rem;border-radius:1rem;background:rgba(52,211,153,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;border:1px solid rgba(52,211,153,0.2);">
                    <svg class="ui-empty-icon" style="color:#34d399;width:2rem;height:2rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 style="font-size:1.25rem;font-weight:800;color:white;margin-bottom:0.5rem;letter-spacing:-0.02em;">No hay IPs bloqueadas</h3>
                <p style="font-size:0.95rem;color:rgba(148,163,184,0.7);max-width:25rem;margin:0 auto;line-height:1.5;">El sistema Anti-Spam está monitoreando, pero no se ha detectado tráfico malicioso reciente.</p>
            </div>
        @endif
    </div>
</div>

@endsection
