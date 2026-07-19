@extends('admin.layouts.app')

@section('title', 'Gestión de Garantías - Panel de Administración')
@section('header', '')
@section('description', '')

@section('content')
<style>
/* ===== MODAL GLASS ===== */
.ui-modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); z-index:9999; align-items:center; justify-content:center; padding:1rem; }
.ui-modal-backdrop.active { display:flex; }
.ui-modal-box { background:rgba(15,20,50,0.95); border:1px solid rgba(168,85,247,0.3); border-radius:1.5rem; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 60px rgba(0,0,0,0.8), 0 0 40px rgba(168,85,247,0.15); position:relative; animation:modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
@keyframes modalIn { from { opacity:0; transform: scale(0.95) translateY(20px); } to { opacity:1; transform: scale(1) translateY(0); } }
.ui-modal-box::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,#a855f7,#ec4899,transparent); border-radius:1.5rem 1.5rem 0 0; }
.ui-modal-header { padding:1.5rem 1.75rem; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:space-between; }
.ui-modal-header h3 { font-size:1.1rem; font-weight:900; text-transform:uppercase; letter-spacing:0.1em; background:linear-gradient(135deg,#a855f7,#ec4899); -webkit-background-clip:text; -webkit-text-fill-color:transparent; display:flex; align-items:center; gap:0.5rem; margin:0; }
.ui-modal-close { width:2.2rem; height:2.2rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; color:rgba(148,163,184,0.7); transition:all 0.2s; flex-shrink:0; }
.ui-modal-close:hover { background:rgba(239,68,68,0.15); border-color:rgba(239,68,68,0.4); color:#f87171; transform:scale(1.1); }
.ui-modal-body { padding:1.5rem 1.75rem; }
.ui-modal-footer { padding:1.25rem 1.75rem; border-top:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:space-between; background:rgba(0,0,0,0.2); }

/* Info grid in modal */
.ui-info-grid { background:rgba(168,85,247,0.05); border:1px solid rgba(168,85,247,0.15); border-radius:1rem; padding:1.25rem; margin-bottom:1.5rem; display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.ui-info-item { font-size:0.85rem; }
.ui-info-item .lbl { color:rgba(148,163,184,0.7); font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.3rem; }
.ui-info-item .val { color:white; font-weight:700; }
.ui-info-item.full { grid-column:span 2; }

/* Modal inputs */
.ui-modal-label { font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:rgba(52,211,153,0.9); margin-bottom:0.5rem; display:flex; align-items:center; gap:0.3rem; }
.ui-modal-input, .ui-modal-textarea { width:100%; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); border-radius:0.75rem; padding:0.875rem 1rem; color:white; font-size:0.9rem; outline:none; transition:all 0.2s; font-family:inherit; }
.ui-modal-input:focus, .ui-modal-textarea:focus { border-color:rgba(168,85,247,0.5); background:rgba(168,85,247,0.05); box-shadow:0 0 0 3px rgba(168,85,247,0.1); }
.ui-modal-textarea { resize:vertical; min-height:90px; }
</style>

<div class="max-w-7xl mx-auto space-y-8">

    {{-- ── HERO ── --}}
    <div class="ui-hero ui-anim-in">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; width:100%; flex-wrap:wrap; gap:1rem;">
            <div>
                <div class="ui-hero-tag">Soporte Técnico</div>
                <h1 class="ui-hero-title">Gestión de Garantías</h1>
                <p class="ui-hero-sub">Administra los reportes de caídas y reembolsos de los usuarios.</p>
            </div>
            <div style="display:flex; gap:0.75rem;">
                <button onclick="location.reload();" class="ui-btn" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:white;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                @if(!in_array(auth()->user()->role, ['admin', 'superadmin']))
                <button onclick="openReportModal()" class="ui-btn ui-btn-danger">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77-1.333.192 3 1.732 3z"/></svg>
                    Reportar Falla
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ── STATS TOP BAR ── --}}
    <div style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;" class="ui-anim-in ui-delay-1">
        
        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(168,85,247,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(168,85,247,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#c084fc;">{{ $warranties->count() }}</div>
                <div class="ae-metric-label">Total Tickets</div>
            </div>
        </div>

        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(245,158,11,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(245,158,11,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#fcd34d;">{{ $warranties->where('status', 'pending')->count() }}</div>
                <div class="ae-metric-label">Pendientes</div>
            </div>
        </div>

        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(16,185,129,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(16,185,129,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#34d399;">{{ $warranties->where('status', 'approved')->count() }}</div>
                <div class="ae-metric-label">Aprobados</div>
            </div>
        </div>

        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(59,130,246,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(59,130,246,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#60a5fa;">{{ $warranties->where('status', 'resolved')->count() }}</div>
                <div class="ae-metric-label">Resueltos</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="ui-alert ui-alert--success ui-anim-in ui-delay-2" style="margin-bottom:1.5rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── TABLA DE GARANTÍAS ── --}}
    <div class="ae-card ui-anim-in ui-delay-3">
        <div class="ae-card-head">
            <div class="ae-card-title">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ui-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    Lista de Tickets
                </div>
            </div>
        </div>
        
        <div class="ae-card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table class="ae-table">
                    <thead>
                        <tr>
                            <th>Ticket / Fecha</th>
                            <th>Cliente Asignado</th>
                            <th>Cuenta Afectada</th>
                            <th>Tipo / Plataforma</th>
                            <th>Motivo del Cliente</th>
                            <th>Estado Actual</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warranties as $warranty)
                            <tr>
                                {{-- ID / FECHA --}}
                                <td>
                                    <div style="font-weight:800;color:white;font-size:0.875rem;">#{{ $warranty->id }}</div>
                                    <div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.25rem;">
                                        <span style="font-size:0.65rem;color:rgba(148,163,184,0.6);text-transform:uppercase;font-weight:700;">
                                            Hace {{ \Carbon\Carbon::parse($warranty->created_at)->diffForHumans(null, true) }}
                                        </span>
                                    </div>
                                </td>
                                
                                {{-- CLIENTE --}}
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.75rem;">
                                        <div class="ae-monogram">
                                            {{ strtoupper(substr($warranty->client ? $warranty->client->name : 'N', 0, 1)) }}
                                        </div>
                                        <div style="font-weight:800;color:white;font-size:0.875rem;">
                                            {{ $warranty->client ? $warranty->client->name : 'N/A' }}
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- CUENTA AFECTADA --}}
                                <td>
                                    <div style="font-weight:700;color:#fca5a5;font-size:0.875rem;">
                                        {{ $warranty->old_email }}
                                    </div>
                                </td>
                                
                                {{-- TIPO Y PLATAFORMA --}}
                                <td>
                                    <div style="display:flex;flex-direction:column;align-items:flex-start;gap:0.4rem;">
                                        @if($warranty->type === 'replacement')
                                            <span class="ui-badge-neon error" style="font-size:0.6rem;">Reemplazo Total</span>
                                        @else
                                            <span class="ui-badge-neon warning" style="font-size:0.6rem;">Problema Menor</span>
                                        @endif
                                        
                                        @if($warranty->platform)
                                            <span class="ui-badge-neon" style="font-size:0.6rem; color:#818cf8; border-color:rgba(129,140,248,0.2); background:rgba(129,140,248,0.05);">
                                                {{ $warranty->platform->name }}
                                            </span>
                                        @else
                                            <span style="font-size:0.7rem;color:rgba(148,163,184,0.4);font-style:italic;">Sin plataforma</span>
                                        @endif
                                    </div>
                                </td>
                                
                                {{-- MOTIVO --}}
                                <td>
                                    <div style="font-size:0.8rem;color:rgba(148,163,184,0.8);font-style:italic;line-height:1.4;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $warranty->reason }}">
                                        "{{ $warranty->reason }}"
                                    </div>
                                </td>
                                
                                {{-- ESTADO --}}
                                <td>
                                    @if($warranty->status === 'pending')
                                        <span class="ui-badge-neon" style="color:#94a3b8; border-color:rgba(148,163,184,0.3); background:rgba(148,163,184,0.1);">Pendiente</span>
                                    @elseif($warranty->status === 'approved')
                                        <span class="ui-badge-neon success">Aprobado</span>
                                    @elseif($warranty->status === 'rejected')
                                        <span class="ui-badge-neon error">Rechazado</span>
                                    @elseif($warranty->status === 'resolved')
                                        <span class="ui-badge-neon" style="color:#38bdf8; border-color:rgba(56,189,248,0.2); background:rgba(56,189,248,0.1);">Resuelto</span>
                                    @else
                                        <span class="ui-badge-neon" style="color:#cbd5e1; border-color:rgba(203,213,225,0.3); background:rgba(203,213,225,0.1);">{{ $warranty->status ? ucfirst($warranty->status) : 'Desconocido' }}</span>
                                    @endif
                                </td>
                                
                                {{-- ACCIONES --}}
                                <td style="text-align:right;">
                                    @if($warranty->status === 'pending')
                                        @if(auth()->user()->role === 'admin')
                                            <button onclick="openWarrantyModal({{ $warranty->id }})" class="ae-edit-btn" style="color:#34d399; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2);">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Resolver
                                            </button>
                                        @else
                                            <span class="ui-badge-neon warning">En Revisión</span>
                                        @endif
                                    @else
                                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.3rem;">
                                            @if($warranty->status === 'rejected')
                                                <span class="ui-badge-neon error" style="opacity:0.7;">
                                                    Rechazado
                                                </span>
                                            @else
                                                <span class="ui-badge-neon success">
                                                    Gestionado
                                                </span>
                                            @endif
                                            @if($warranty->new_email)
                                                <div style="font-size:0.75rem;color:rgba(16,185,129,0.8);font-weight:700;">{{ $warranty->new_email }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:4rem 2rem;text-align:center;">
                                    <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:rgba(168,85,247,0.4);">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <h3 style="font-size:1.1rem;font-weight:800;color:white;margin-bottom:0.35rem;">Bandeja Limpia</h3>
                                    <p style="font-size:0.85rem;color:rgba(148,163,184,0.6);">No hay solicitudes de garantía en este momento.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}
@foreach($warranties as $warranty)
    @if($warranty->status === 'pending')
    <div id="resolveModal{{ $warranty->id }}" class="ui-modal-backdrop" onclick="handleBackdropClick(event, {{ $warranty->id }})">
        <div class="ui-modal-box" onclick="event.stopPropagation()">
            {{-- Header --}}
            <div class="ui-modal-header">
                <h3>
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Resolver Ticket #{{ $warranty->id }}
                </h3>
                <button type="button" class="ui-modal-close" onclick="closeWarrantyModal({{ $warranty->id }})">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="ui-modal-body">
                {{-- Info --}}
                <div class="ui-info-grid">
                    <div class="ui-info-item">
                        <div class="lbl">Categoría</div>
                        <div class="val">
                            @if($warranty->type === 'replacement')
                                <span class="ui-badge-neon error" style="font-size:0.6rem;">Reemplazo</span>
                            @else
                                <span class="ui-badge-neon warning" style="font-size:0.6rem;">Menor</span>
                            @endif
                        </div>
                    </div>
                    <div class="ui-info-item">
                        <div class="lbl">Cliente</div>
                        <div class="val">{{ $warranty->client->name ?? 'N/A' }}</div>
                    </div>
                    <div class="ui-info-item full">
                        <div class="lbl">Cuenta Afectada</div>
                        <div class="val" style="color:#c084fc;font-size:1.05rem;">{{ $warranty->old_email }}</div>
                    </div>
                    <div class="ui-info-item full">
                        <div class="lbl">Descripción del Problema</div>
                        <div class="val" style="font-weight:500;color:rgba(226,232,240,0.9);font-style:italic;">"{{ $warranty->reason }}"</div>
                    </div>
                </div>

                <form id="resolveForm{{ $warranty->id }}" action="{{ route('admin.warranties.update', $warranty->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if($warranty->type === 'replacement')
                        <div style="margin-bottom:1.5rem;">
                            <label class="ui-modal-label">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Entregar Nuevo Correo
                            </label>
                            <input type="email" name="new_email" class="ui-modal-input" placeholder="correo.nuevo@plataforma.com" required>
                            <p style="font-size:0.75rem;color:rgba(148,163,184,0.6);margin-top:0.5rem;font-weight:600;">
                                Automáticamente desvincula la cuenta anterior y repone los días perdidos al cliente.
                            </p>
                            <input type="hidden" name="status" value="approved">
                        </div>
                    @else
                        <div style="background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.15);border-radius:0.75rem;padding:1rem;color:#fcd34d;font-size:0.85rem;font-weight:600;display:flex;align-items:flex-start;gap:0.75rem;margin-bottom:1.5rem;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Al confirmar, la solicitud se marcará como resuelta y los días pausados se reanudarán en el plan del cliente de manera automática.</span>
                        </div>
                        <input type="hidden" name="status" value="resolved">
                    @endif

                    <div>
                        <label class="ui-modal-label" style="color:white;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            Mensaje para el Cliente (Opcional)
                        </label>
                        <textarea name="admin_notes" class="ui-modal-textarea" placeholder="Escribe instrucciones adicionales o la nueva contraseña aquí..."></textarea>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="ui-modal-footer">
                <button type="button" class="ui-btn ui-btn-cancel" onclick="submitRejectForm({{ $warranty->id }})" style="color:#f87171; border-color:rgba(239,68,68,0.2); background:rgba(239,68,68,0.05);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Rechazar
                </button>
                <div style="display:flex;gap:0.75rem;">
                    <button type="button" class="ui-btn ui-btn-cancel" onclick="closeWarrantyModal({{ $warranty->id }})">Cancelar</button>
                    <button type="submit" form="resolveForm{{ $warranty->id }}" class="ui-btn ui-btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Solucionar Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

    {{-- MODAL REPORTAR GARANTÍA --}}
    @if(!in_array(auth()->user()->role, ['admin', 'superadmin']))
    <div id="reportModal" class="ui-modal-backdrop" onclick="closeReportModal()">
        <div class="ui-modal-box" onclick="event.stopPropagation()">
            <div class="ui-modal-header">
                <h3><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77-1.333.192 3 1.732 3z"/></svg> Reportar Nueva Falla</h3>
                <button type="button" class="ui-modal-close" onclick="closeReportModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('admin.warranties.store') }}" method="POST">
                @csrf
                <div class="ui-modal-body">
                    <p style="color:rgba(148,163,184,0.8);font-size:0.85rem;margin-bottom:1.5rem;line-height:1.4;">
                        Al reportar una falla, el sistema congelará el tiempo de la cuenta automáticamente hasta que un administrador la resuelva.
                    </p>

                    <div style="margin-bottom:1.5rem;">
                        <label class="ui-modal-label">¿De quién es la cuenta afectada?</label>
                        <div style="display:flex;gap:1.5rem;margin-top:0.75rem;">
                            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;color:white;font-size:0.85rem;font-weight:600;">
                                <input type="radio" name="report_type" value="client" checked onchange="toggleReportType()" style="accent-color:#a855f7;">
                                Cuenta de un Cliente
                            </label>
                            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;color:white;font-size:0.85rem;font-weight:600;">
                                <input type="radio" name="report_type" value="personal" onchange="toggleReportType()" style="accent-color:#a855f7;">
                                Cuenta Propia (Stock)
                            </label>
                        </div>
                    </div>

                    <div id="client_selection_wrapper" style="margin-bottom:1.25rem;">
                        <label class="ui-modal-label">1. Seleccionar Cliente</label>
                        <select name="client_id" id="client_select" class="ui-modal-input" required onchange="updateEmails()">
                            <option value="" style="background:#050510;">-- Elige un cliente --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" data-emails="{{ json_encode($c->allowedEmails->pluck('email')) }}" style="background:#050510;">{{ $c->name }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label class="ui-modal-label">2. Cuenta Afectada</label>
                        <select name="old_email" id="email_select" class="ui-modal-input" required disabled>
                            <option value="" style="background:#050510;">-- Primero elige el origen --</option>
                        </select>
                    </div>

                    <div style="margin-bottom:1.25rem;">
                        <label class="ui-modal-label">3. Tipo de Problema</label>
                        <select name="type" class="ui-modal-input" required>
                            <option value="minor_issue" style="background:#050510;">Problema Menor (Caída temporal, Clave cambiada)</option>
                            <option value="replacement" style="background:#050510;">Caída Definitiva (Requiere reemplazo total)</option>
                        </select>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <label class="ui-modal-label">4. Descripción / Motivo</label>
                        <textarea name="reason" class="ui-modal-textarea" placeholder="Explica detalladamente qué pasó con la cuenta..." required></textarea>
                    </div>
                </div>
                
                <div class="ui-modal-footer">
                    <button type="button" class="ui-btn ui-btn-cancel" onclick="closeReportModal()">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-danger">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 
                        Enviar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

@push('scripts')
<script>
    const unassignedEmails = {!! json_encode($unassignedEmails ?? []) !!};

    function openWarrantyModal(id) {
        const modal = document.getElementById('resolveModal' + id);
        if (modal) modal.classList.add('active');
    }
    
    function closeWarrantyModal(id) {
        const modal = document.getElementById('resolveModal' + id);
        if (modal) modal.classList.remove('active');
    }
    
    function handleBackdropClick(event, id) {
        if (event.target === event.currentTarget) {
            closeWarrantyModal(id);
        }
    }
    
    function submitRejectForm(id) {
        if(confirm('¿Estás seguro de que deseas rechazar esta solicitud de garantía?')) {
            const form = document.getElementById('resolveForm' + id);
            
            let statusInput = form.querySelector('input[name="status"]');
            if(statusInput) statusInput.value = 'rejected';
            
            let emailInput = form.querySelector('input[name="new_email"]');
            if(emailInput) emailInput.removeAttribute('required');
            
            form.submit();
        }
    }

    function openReportModal() {
        document.getElementById('reportModal').classList.add('active');
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.remove('active');
    }

    function toggleReportType() {
        const type = document.querySelector('input[name="report_type"]:checked').value;
        const clientWrapper = document.getElementById('client_selection_wrapper');
        const clientSelect = document.getElementById('client_select');
        const emailSelect = document.getElementById('email_select');

        if (type === 'client') {
            clientWrapper.style.display = 'block';
            clientSelect.setAttribute('required', 'required');
            updateEmails();
        } else {
            clientWrapper.style.display = 'none';
            clientSelect.removeAttribute('required');
            
            // Llenar con correos sin asignar
            emailSelect.innerHTML = '<option value="" style="background:#050510;">-- Elige un correo propio --</option>';
            if (unassignedEmails.length > 0) {
                unassignedEmails.forEach(email => {
                    const opt = document.createElement('option');
                    opt.value = email;
                    opt.textContent = email;
                    opt.style.background = '#050510';
                    emailSelect.appendChild(opt);
                });
                emailSelect.disabled = false;
            } else {
                emailSelect.innerHTML = '<option value="" style="background:#050510;">-- Sin correos en inventario --</option>';
                emailSelect.disabled = true;
            }
        }
    }

    function updateEmails() {
        const type = document.querySelector('input[name="report_type"]:checked').value;
        if (type === 'personal') return;

        const clientSelect = document.getElementById('client_select');
        const emailSelect = document.getElementById('email_select');
        const selectedOption = clientSelect.options[clientSelect.selectedIndex];
        
        emailSelect.innerHTML = '<option value="" style="background:#050510;">-- Elige un correo --</option>';
        emailSelect.disabled = true;

        if (selectedOption && selectedOption.value !== "") {
            const emails = JSON.parse(selectedOption.getAttribute('data-emails'));
            if (emails && emails.length > 0) {
                emails.forEach(email => {
                    const opt = document.createElement('option');
                    opt.value = email;
                    opt.textContent = email;
                    opt.style.background = '#050510';
                    emailSelect.appendChild(opt);
                });
                emailSelect.disabled = false;
            } else {
                emailSelect.innerHTML = '<option value="" style="background:#050510;">-- Sin correos asignados --</option>';
            }
        }
    }
</script>
@endpush
@endsection
