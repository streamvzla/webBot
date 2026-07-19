@extends('client.layouts.app')
@section('title','Mis Garantias')
@section('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:"Poppins",sans-serif;}
.war-page{max-width:1000px;margin:0 auto;padding:2rem 1rem 8rem;position:relative;}
.orb{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0;}
.orb-1{width:500px;height:500px;background:rgba(124,58,237,0.1);top:-150px;left:-100px;}
.orb-2{width:400px;height:400px;background:rgba(236,72,153,0.07);top:300px;right:-150px;}

.war-hero{background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.08);border-top:2px solid rgba(168,85,247,0.5);border-radius:1.5rem;padding:2rem 2.5rem;display:flex;align-items:center;justify-content:space-between;gap:1.5rem;flex-wrap:wrap;backdrop-filter:blur(12px);margin-bottom:1.5rem;position:relative;z-index:1;transition:border-color 0.3s;}
.war-hero:hover{border-top-color:rgba(168,85,247,0.8);}
.war-hero-title{font-size:1.875rem;font-weight:900;letter-spacing:-0.03em;background:linear-gradient(135deg,#fff 0%,#c4b5fd 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:0.25rem;}
.war-hero-sub{color:rgba(148,163,184,0.65);font-size:0.9rem;}
.pending-alert{display:inline-flex;align-items:center;gap:0.5rem;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.3);color:#fbbf24;font-size:0.78rem;font-weight:700;padding:0.35rem 0.875rem;border-radius:9999px;margin-top:0.5rem;animation:pendingPulse 2s ease-in-out infinite;}
@keyframes pendingPulse{0%,100%{opacity:1}50%{opacity:0.7}}

.btn-war{display:inline-flex;align-items:center;justify-content:center;gap:0.625rem;background:linear-gradient(135deg,#7c3aed,#a855f7,#ec4899);border:none;border-radius:0.875rem;color:white;font-weight:800;font-size:0.9rem;letter-spacing:0.02em;padding:0.875rem 1.75rem;cursor:pointer;text-decoration:none;box-shadow:0 8px 25px rgba(168,85,247,0.4);transition:all 0.3s cubic-bezier(0.16,1,0.3,1);position:relative;overflow:hidden;white-space:nowrap;}
.btn-war::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);transform:translateX(-100%);transition:transform 0.5s ease;}
.btn-war:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(168,85,247,0.55);}
.btn-war:hover::after{transform:translateX(100%);}
.btn-war:disabled{opacity:0.4;cursor:not-allowed;transform:none !important;}

.btn-cancel-row{display:inline-flex;align-items:center;gap:0.375rem;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);color:#f87171;font-size:0.72rem;font-weight:700;padding:0.35rem 0.75rem;border-radius:0.5rem;cursor:pointer;transition:all 0.2s;white-space:nowrap;}
.btn-cancel-row:hover{background:rgba(239,68,68,0.18);border-color:rgba(239,68,68,0.5);}

.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;position:relative;z-index:1;}
@media(max-width:500px){.stats-grid{grid-template-columns:1fr;}}
.stat-card{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:1.25rem;padding:1.25rem 1.5rem;backdrop-filter:blur(8px);transition:border-color 0.3s,transform 0.3s;display:flex;align-items:center;gap:1rem;}
.stat-card:hover{border-color:rgba(168,85,247,0.3);transform:translateY(-2px);}
.stat-icon{width:2.75rem;height:2.75rem;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-val{font-size:1.75rem;font-weight:900;color:white;line-height:1;}
.stat-lbl{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(100,116,139,0.8);margin-top:0.2rem;}

.glass-tbl{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:1.5rem;overflow:hidden;backdrop-filter:blur(12px);position:relative;z-index:1;}
.tbl-head{padding:1.25rem 1.75rem;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
.tbl-head-title{font-size:0.8rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:rgba(168,85,247,0.8);}

.war-table{width:100%;border-collapse:collapse;}
.war-table thead tr{border-bottom:1px solid rgba(255,255,255,0.06);}
.war-table th{padding:1rem 1.25rem;font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:rgba(100,116,139,0.9);text-align:left;white-space:nowrap;}
.war-table tbody tr{border-bottom:1px solid rgba(255,255,255,0.04);transition:background 0.2s;cursor:pointer;}
.war-table tbody tr:last-child{border-bottom:none;}
.war-table tbody tr:hover{background:rgba(168,85,247,0.06);}
.war-table td{padding:1rem 1.25rem;font-size:0.875rem;color:rgba(226,232,240,0.9);vertical-align:middle;}

.badge{display:inline-flex;align-items:center;gap:0.375rem;padding:0.3rem 0.75rem;border-radius:9999px;font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;white-space:nowrap;}
.bdot{width:5px;height:5px;border-radius:50%;display:inline-block;}
.b-pending{background:rgba(251,191,36,0.1);color:#fbbf24;border:1px solid rgba(251,191,36,0.3);}
.b-pending .bdot{background:#fbbf24;box-shadow:0 0 6px #fbbf24;animation:bdot 1.5s infinite;}
.b-approved{background:rgba(52,211,153,0.1);color:#34d399;border:1px solid rgba(52,211,153,0.3);}
.b-approved .bdot{background:#34d399;box-shadow:0 0 6px #34d399;}
.b-rejected{background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.3);}
.b-rejected .bdot{background:#f87171;box-shadow:0 0 6px #f87171;}
.b-resolved{background:rgba(168,85,247,0.1);color:#c4b5fd;border:1px solid rgba(168,85,247,0.3);}
.b-resolved .bdot{background:#c4b5fd;box-shadow:0 0 6px #c4b5fd;}
.b-cancelled{background:rgba(100,116,139,0.1);color:#94a3b8;border:1px solid rgba(100,116,139,0.3);}
.b-cancelled .bdot{background:#94a3b8;box-shadow:0 0 6px #94a3b8;}
.b-replacement{background:rgba(239,68,68,0.08);color:#f87171;border:1px solid rgba(239,68,68,0.2);}
.b-minor{background:rgba(251,191,36,0.08);color:#fbbf24;border:1px solid rgba(251,191,36,0.2);}
@keyframes bdot{0%,100%{opacity:1}50%{opacity:0.3}}

.empty-state{padding:5rem 2rem;text-align:center;display:flex;flex-direction:column;align-items:center;gap:1.25rem;}
.empty-icon{width:5rem;height:5rem;background:rgba(168,85,247,0.08);border:1px solid rgba(168,85,247,0.2);border-radius:1.25rem;display:flex;align-items:center;justify-content:center;color:rgba(168,85,247,0.6);}

.mob-card-list{display:none;padding:1rem;flex-direction:column;gap:0.75rem;}
.mob-card{background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.08);border-radius:1.25rem;padding:1.25rem;cursor:pointer;transition:border-color 0.2s;}
.mob-card:hover{border-color:rgba(168,85,247,0.3);}
.mob-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;gap:0.5rem;flex-wrap:wrap;}
.mob-label{font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.8);}
.mob-val{font-size:0.85rem;color:rgba(226,232,240,0.9);}
@media(max-width:680px){.war-table-wrap{display:none;}.mob-card-list{display:flex;}}

.alert{display:flex;align-items:center;gap:0.875rem;padding:1rem 1.5rem;border-radius:1rem;margin-bottom:1.25rem;font-size:0.9rem;font-weight:600;backdrop-filter:blur(8px);}
.alert-ok{background:rgba(52,211,153,0.06);border:1px solid rgba(52,211,153,0.3);color:#34d399;}
.alert-err{background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.3);color:#f87171;}
.paused-notice{background:rgba(251,191,36,0.07);border:1px solid rgba(251,191,36,0.3);border-radius:1rem;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.875rem;font-size:0.85rem;color:#fbbf24;font-weight:600;}

.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.82);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);z-index:9999;align-items:center;justify-content:center;padding:1rem;}
.modal-bg.open{display:flex;}
.modal-box{background:linear-gradient(145deg,rgba(18,10,45,0.99) 0%,rgba(8,4,20,1) 100%);border:1px solid rgba(168,85,247,0.3);border-radius:1.5rem;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;box-shadow:0 30px 80px rgba(0,0,0,0.8),0 0 60px rgba(168,85,247,0.1);position:relative;animation:modalIn 0.35s cubic-bezier(0.16,1,0.3,1) both;}
@keyframes modalIn{from{transform:translateY(24px) scale(0.96);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#7c3aed,#a855f7,#ec4899,transparent);}
.modal-hd{padding:1.75rem 2rem 1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;}
.modal-bd{padding:0 2rem 1.5rem;}
.modal-ft{padding:1.25rem 2rem;border-top:1px solid rgba(255,255,255,0.06);display:flex;justify-content:flex-end;gap:0.875rem;flex-wrap:wrap;}
.modal-close{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:0.625rem;color:rgba(148,163,184,0.6);width:2.25rem;height:2.25rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;flex-shrink:0;}
.modal-close:hover{background:rgba(239,68,68,0.12);border-color:rgba(239,68,68,0.3);color:#f87171;}

.m-label{display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(148,163,184,0.7);margin-bottom:0.5rem;}
.m-input{width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(168,85,247,0.2);border-radius:0.875rem;padding:0.875rem 1.25rem;color:white;font-size:0.9rem;transition:all 0.25s;outline:none;}
.m-input:focus{border-color:#a855f7;background:rgba(168,85,247,0.05);box-shadow:0 0 0 3px rgba(168,85,247,0.15);}
.m-input option{background:#120a2d;color:white;}
.char-count{font-size:0.7rem;color:rgba(100,116,139,0.7);text-align:right;margin-top:0.35rem;}
.char-count.warn{color:#fbbf24;}.char-count.danger{color:#f87171;}
.info-box{background:rgba(168,85,247,0.07);border:1px solid rgba(168,85,247,0.2);border-radius:0.875rem;padding:0.875rem 1rem;margin-bottom:1.25rem;font-size:0.8rem;color:rgba(196,181,253,0.85);display:flex;align-items:flex-start;gap:0.625rem;}
.btn-cancel-modal{background:rgba(255,255,255,0.03);border:1px dashed rgba(255,255,255,0.12);border-radius:0.875rem;color:rgba(148,163,184,0.6);font-size:0.9rem;font-weight:700;padding:0.875rem 1.5rem;cursor:pointer;transition:all 0.2s;}
.btn-cancel-modal:hover{background:rgba(255,255,255,0.07);color:white;border-style:solid;border-color:rgba(255,255,255,0.2);}

.confirm-row{display:flex;justify-content:space-between;align-items:flex-start;padding:0.75rem 0;border-bottom:1px solid rgba(255,255,255,0.05);gap:1rem;}
.confirm-row:last-child{border-bottom:none;}
.confirm-key{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(100,116,139,0.8);flex-shrink:0;padding-top:2px;}
.confirm-val{font-size:0.875rem;color:rgba(226,232,240,0.9);text-align:right;font-weight:500;word-break:break-word;max-width:300px;}

.detail-field{margin-bottom:1.25rem;}
.detail-label{font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:rgba(100,116,139,0.8);margin-bottom:0.4rem;}
.detail-val{font-size:0.9rem;color:rgba(226,232,240,0.9);line-height:1.6;word-break:break-all;}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
@media(max-width:480px){.detail-grid{grid-template-columns:1fr;}}
.no-emails-notice{background:rgba(100,116,139,0.07);border:1px solid rgba(100,116,139,0.2);border-radius:0.875rem;padding:0.875rem 1rem;font-size:0.82rem;color:rgba(148,163,184,0.8);display:flex;align-items:center;gap:0.625rem;}

@media(max-width:640px){
    .war-page{padding:1.5rem 0.5rem 6rem;}
    .war-hero{padding:1.25rem;flex-direction:column;align-items:stretch;}
    .stat-card{padding:1rem 1.25rem;}
    .btn-war{width:100%;}
    .stats-grid{grid-template-columns:1fr;}
}
</style>
@endsection

@section('content')
@php
    $total            = $warranties->count();
    $pending          = $warranties->where('status','pending')->count();
    $resolved         = $warranties->whereIn('status',['resolved','approved'])->count();
    $hasPaused        = auth('client')->user()->allowedEmails()->whereNotNull('paused_at')->exists();
    $hasAvailEmails   = $allowedEmails->count() > 0;
@endphp

<div class="war-page">
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

@if(session('success'))
<div class="alert alert-ok">
    <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-err">
    <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif

@if($hasPaused)
<div class="paused-notice">
    <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>Una o varias cuentas estan <strong>pausadas</strong> por una garantia activa. No podras consultar codigos hasta que el administrador la resuelva.</span>
</div>
@endif

{{-- HERO --}}
<div class="war-hero">
    <div>
        <h1 class="war-hero-title">Mis Garantias</h1>
        <p class="war-hero-sub">Historial de solicitudes y reemplazos de cuenta</p>
        @if($pending > 0)
        <span class="pending-alert">
            <svg style="width:0.75rem;height:0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01"/></svg>
            {{ $pending }} {{ $pending == 1 ? 'solicitud pendiente' : 'solicitudes pendientes' }}
        </span>
        @endif
    </div>
    <button type="button" onclick="openNewModal()" class="btn-war" @if(!$hasAvailEmails) disabled title="Todas tus cuentas tienen solicitudes activas" @endif>
        <svg style="width:1.1rem;height:1.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Solicitar Garantia
    </button>
</div>

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(168,85,247,0.1);border:1px solid rgba(168,85,247,0.2);">
            <svg style="width:1.25rem;height:1.25rem;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div><div class="stat-val">{{ $total }}</div><div class="stat-lbl">Total</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.2);">
            <svg style="width:1.25rem;height:1.25rem;color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="stat-val">{{ $pending }}</div><div class="stat-lbl">Pendientes</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.2);">
            <svg style="width:1.25rem;height:1.25rem;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><div class="stat-val">{{ $resolved }}</div><div class="stat-lbl">Resueltas</div></div>
    </div>
</div>

{{-- TABLE --}}
<div class="glass-tbl">
    <div class="tbl-head">
        <span class="tbl-head-title">Historial de Solicitudes</span>
        <span style="font-size:0.78rem;color:rgba(100,116,139,0.7);">{{ $total }} {{ $total == 1 ? 'registro' : 'registros' }}</span>
    </div>

    @if($warranties->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <svg style="width:2.5rem;height:2.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <p style="color:white;font-size:1.05rem;font-weight:800;">Sin solicitudes de garantia</p>
            <p style="color:rgba(148,163,184,0.55);font-size:0.85rem;margin-top:0.35rem;">Cuando tengas un problema, haz clic en "Solicitar Garantia"</p>
        </div>
    </div>
    @else

    {{-- DESKTOP --}}
    <div class="war-table-wrap" style="overflow-x:auto;">
        <table class="war-table">
            <thead><tr>
                <th>Fecha</th><th>Cuenta Afectada</th><th>Tipo</th><th>Plataforma</th><th>Estado</th><th>Acciones</th>
            </tr></thead>
            <tbody>
            @foreach($warranties as $w)
            <tr onclick="openDetail({{ $w->id }},'{{ addslashes($w->old_email) }}','{{ $w->type }}','{{ $w->status }}','{{ $w->platform ? addslashes($w->platform->name) : 'N/A' }}','{{ addslashes($w->reason) }}','{{ addslashes($w->admin_notes ?? '') }}','{{ $w->new_email ?? '' }}','{{ $w->created_at->format('d/m/Y H:i') }}')">
                <td style="color:rgba(100,116,139,0.8);font-size:0.78rem;white-space:nowrap;">{{ $w->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-family:monospace;color:#c4b5fd;font-size:0.82rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $w->old_email }}">{{ $w->old_email }}</td>
                <td>@if($w->type==='replacement')<span class="badge b-replacement">Reemplazo</span>@else<span class="badge b-minor">Menor</span>@endif</td>
                <td style="color:rgba(148,163,184,0.8);font-size:0.85rem;">{{ $w->platform ? $w->platform->name : 'N/A' }}</td>
                <td>
                    @if($w->status==='pending')<span class="badge b-pending"><span class="bdot"></span>Pendiente</span>
                    @elseif($w->status==='approved')<span class="badge b-approved"><span class="bdot"></span>Aprobado</span>
                    @elseif($w->status==='rejected')<span class="badge b-rejected"><span class="bdot"></span>Rechazado</span>
                    @elseif($w->status==='resolved')<span class="badge b-resolved"><span class="bdot"></span>Resuelto</span>
                    @elseif($w->status==='cancelled')<span class="badge b-cancelled"><span class="bdot"></span>Cancelado</span>@endif
                </td>
                <td onclick="event.stopPropagation()">
                    @if($w->status==='pending')
                    <form method="POST" action="{{ route('client.warranties.destroy',$w->id) }}" onsubmit="return confirm('Cancelar esta solicitud? Tu cuenta sera reactivada.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-cancel-row">
                            <svg style="width:0.75rem;height:0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>Cancelar
                        </button>
                    </form>
                    @else<span style="font-size:0.75rem;color:rgba(100,116,139,0.4);">—</span>@endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="mob-card-list">
        @foreach($warranties as $w)
        <div class="mob-card" onclick="openDetail({{ $w->id }},'{{ addslashes($w->old_email) }}','{{ $w->type }}','{{ $w->status }}','{{ $w->platform ? addslashes($w->platform->name) : 'N/A' }}','{{ addslashes($w->reason) }}','{{ addslashes($w->admin_notes ?? '') }}','{{ $w->new_email ?? '' }}','{{ $w->created_at->format('d/m/Y H:i') }}')">
            <div class="mob-row">
                <span style="font-family:monospace;color:#c4b5fd;font-size:0.82rem;font-weight:600;">{{ Str::limit($w->old_email,28) }}</span>
                @if($w->status==='pending')<span class="badge b-pending"><span class="bdot"></span>Pendiente</span>
                @elseif($w->status==='approved')<span class="badge b-approved"><span class="bdot"></span>Aprobado</span>
                @elseif($w->status==='rejected')<span class="badge b-rejected"><span class="bdot"></span>Rechazado</span>
                @elseif($w->status==='resolved')<span class="badge b-resolved"><span class="bdot"></span>Resuelto</span>
                @elseif($w->status==='cancelled')<span class="badge b-cancelled"><span class="bdot"></span>Cancelado</span>@endif
            </div>
            <div class="mob-row"><span class="mob-label">Tipo</span>@if($w->type==='replacement')<span class="badge b-replacement">Reemplazo</span>@else<span class="badge b-minor">Menor</span>@endif</div>
            <div class="mob-row"><span class="mob-label">Plataforma</span><span class="mob-val">{{ $w->platform ? $w->platform->name : 'N/A' }}</span></div>
            <div class="mob-row"><span class="mob-label">Fecha</span><span class="mob-val" style="font-size:0.78rem;color:rgba(100,116,139,0.8);">{{ $w->created_at->format('d/m/Y H:i') }}</span></div>
            @if($w->status==='pending')
            <div style="margin-top:0.875rem;" onclick="event.stopPropagation()">
                <form method="POST" action="{{ route('client.warranties.destroy',$w->id) }}" onsubmit="return confirm('Cancelar esta solicitud? Tu cuenta sera reactivada.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-cancel-row" style="width:100%;justify-content:center;">
                        <svg style="width:0.75rem;height:0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>Cancelar Solicitud
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
</div>

{{-- ═══ MODAL NUEVA SOLICITUD (2 pasos) ═══ --}}
<div id="newModal" class="modal-bg" onclick="if(event.target===this)closeNewModal()">
    <div class="modal-box">
        <div id="step1">
            <div class="modal-hd">
                <div style="display:flex;align-items:center;gap:0.875rem;">
                    <div style="width:2.75rem;height:2.75rem;background:linear-gradient(135deg,rgba(124,58,237,0.25),rgba(236,72,153,0.15));border:1px solid rgba(168,85,247,0.35);border-radius:0.875rem;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:1.25rem;height:1.25rem;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:1.05rem;font-weight:800;color:white;">Nueva Solicitud</h3>
                        <p style="font-size:0.72rem;color:rgba(148,163,184,0.5);">Paso 1 de 2 — Datos del problema</p>
                    </div>
                </div>
                <button type="button" class="modal-close" onclick="closeNewModal()"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="modal-bd">
                @if(!$hasAvailEmails)
                <div class="no-emails-notice">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Todas tus cuentas tienen solicitudes activas. Espera a que el administrador las gestione antes de reportar una nueva.
                </div>
                @else
                <div class="info-box">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;margin-top:1px;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Al enviar, el tiempo de la cuenta se <strong>pausara automaticamente</strong>. Las cuentas ya reportadas no aparecen en el listado.</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:1.25rem;">
                    <div>
                        <label class="m-label">Cuenta Afectada</label>
                        <select id="f_email" class="m-input">
                            <option value="">Seleccione una cuenta...</option>
                            @foreach($allowedEmails as $em)
                            <option value="{{ $em->email }}">{{ $em->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="m-label">Plataforma (Opcional)</label>
                        <select id="f_platform" class="m-input">
                            <option value="">Seleccione la plataforma...</option>
                            @foreach($platforms as $pl)
                            <option value="{{ $pl->id }}" data-name="{{ $pl->name }}">{{ $pl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="m-label">Tipo de Solicitud</label>
                        <select id="f_type" class="m-input">
                            <option value="replacement">Reemplazo de Cuenta (Cuenta Caida)</option>
                            <option value="minor_issue">Problema Menor (Contrasena, Bloqueo)</option>
                        </select>
                    </div>
                    <div>
                        <label class="m-label">Motivo del Fallo <span style="color:rgba(100,116,139,0.6);">(max. 500)</span></label>
                        <textarea id="f_reason" rows="4" class="m-input" style="resize:vertical;" placeholder="Explique brevemente el problema..." maxlength="500" oninput="updateChar()"></textarea>
                        <p id="charCount" class="char-count">0 / 500</p>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-ft">
                <button type="button" class="btn-cancel-modal" onclick="closeNewModal()">Cancelar</button>
                @if($hasAvailEmails)<button type="button" class="btn-war" onclick="goStep2()">Continuar &nbsp;→</button>@endif
            </div>
        </div>

        {{-- STEP 2 CONFIRM --}}
        <div id="step2" style="display:none;">
            <form id="warrantyForm" method="POST" action="{{ route('client.warranties.store') }}">
                @csrf
                <input type="hidden" name="old_email"   id="h_email">
                <input type="hidden" name="platform_id" id="h_platform">
                <input type="hidden" name="type"        id="h_type">
                <input type="hidden" name="reason"      id="h_reason">
                <div class="modal-hd">
                    <div style="display:flex;align-items:center;gap:0.875rem;">
                        <div style="width:2.75rem;height:2.75rem;background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.3);border-radius:0.875rem;display:flex;align-items:center;justify-content:center;">
                            <svg style="width:1.25rem;height:1.25rem;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 style="font-size:1.05rem;font-weight:800;color:white;">Confirmar Solicitud</h3>
                            <p style="font-size:0.72rem;color:rgba(148,163,184,0.5);">Paso 2 de 2 — Revisa antes de enviar</p>
                        </div>
                    </div>
                    <button type="button" class="modal-close" onclick="closeNewModal()"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="modal-bd">
                    <div style="background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.08);border-radius:1rem;padding:1.25rem;">
                        <div class="confirm-row"><span class="confirm-key">Cuenta</span><span class="confirm-val" id="c_email" style="font-family:monospace;color:#c4b5fd;">—</span></div>
                        <div class="confirm-row"><span class="confirm-key">Tipo</span><span class="confirm-val" id="c_type">—</span></div>
                        <div class="confirm-row"><span class="confirm-key">Plataforma</span><span class="confirm-val" id="c_platform">—</span></div>
                        <div class="confirm-row"><span class="confirm-key">Motivo</span><span class="confirm-val" id="c_reason" style="white-space:pre-wrap;">—</span></div>
                    </div>
                    <p style="margin-top:1rem;font-size:0.8rem;color:rgba(251,191,36,0.8);display:flex;align-items:center;gap:0.5rem;">
                        <svg style="width:0.875rem;height:0.875rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tu cuenta sera pausada hasta que el administrador resuelva la solicitud.
                    </p>
                </div>
                <div class="modal-ft">
                    <button type="button" class="btn-cancel-modal" onclick="goStep1()">← Volver</button>
                    <button type="submit" class="btn-war">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL DETALLE ═══ --}}
<div id="detailModal" class="modal-bg" onclick="if(event.target===this)closeDetailModal()">
    <div class="modal-box">
        <div class="modal-hd">
            <div style="display:flex;align-items:center;gap:0.875rem;">
                <div style="width:2.75rem;height:2.75rem;background:rgba(168,85,247,0.12);border:1px solid rgba(168,85,247,0.3);border-radius:0.875rem;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:1.25rem;height:1.25rem;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <h3 style="font-size:1.05rem;font-weight:800;color:white;">Detalle de Solicitud</h3>
                    <p style="font-size:0.72rem;color:rgba(148,163,184,0.5);" id="d_date">—</p>
                </div>
            </div>
            <button type="button" class="modal-close" onclick="closeDetailModal()"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="modal-bd">
            <div class="detail-grid" style="margin-bottom:1.25rem;">
                <div class="detail-field"><div class="detail-label">Estado</div><div id="d_status">—</div></div>
                <div class="detail-field"><div class="detail-label">Tipo</div><div id="d_type">—</div></div>
                <div class="detail-field"><div class="detail-label">Plataforma</div><div class="detail-val" id="d_platform">—</div></div>
                <div class="detail-field"><div class="detail-label">Cuenta Afectada</div><div class="detail-val" id="d_email" style="font-family:monospace;color:#c4b5fd;font-size:0.82rem;">—</div></div>
            </div>
            <div class="detail-field">
                <div class="detail-label">Motivo Reportado</div>
                <div class="detail-val" id="d_reason" style="background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.07);border-radius:0.875rem;padding:1rem;white-space:pre-wrap;word-break:break-word;">—</div>
            </div>
            <div id="d_new_email_wrap" class="detail-field" style="margin-top:1rem;display:none;">
                <div class="detail-label">Cuenta de Reemplazo</div>
                <div class="detail-val" id="d_new_email" style="font-family:monospace;color:#34d399;">—</div>
            </div>
            <div id="d_notes_wrap" class="detail-field" style="margin-top:1rem;display:none;">
                <div class="detail-label">Notas del Administrador</div>
                <div class="detail-val" id="d_notes" style="background:rgba(52,211,153,0.04);border:1px solid rgba(52,211,153,0.2);border-radius:0.875rem;padding:1rem;white-space:pre-wrap;word-break:break-word;">—</div>
            </div>
        </div>
        <div class="modal-ft">
            <button type="button" class="btn-cancel-modal" onclick="closeDetailModal()">Cerrar</button>
        </div>
    </div>
</div>

<script>
var sBadge={pending:'<span class="badge b-pending"><span class="bdot"></span>Pendiente</span>',approved:'<span class="badge b-approved"><span class="bdot"></span>Aprobado</span>',rejected:'<span class="badge b-rejected"><span class="bdot"></span>Rechazado</span>',resolved:'<span class="badge b-resolved"><span class="bdot"></span>Resuelto</span>',cancelled:'<span class="badge b-cancelled"><span class="bdot"></span>Cancelado</span>'};
var tBadge={replacement:'<span class="badge b-replacement">Reemplazo</span>',minor_issue:'<span class="badge b-minor">Menor</span>'};

function openNewModal(){document.getElementById('newModal').classList.add('open');document.body.style.overflow='hidden';}
function closeNewModal(){document.getElementById('newModal').classList.remove('open');document.body.style.overflow='';goStep1();}
function goStep2(){
    var email=document.getElementById('f_email').value,reason=document.getElementById('f_reason').value.trim();
    if(!email){alert('Selecciona una cuenta.');return;}
    if(!reason){alert('Escribe el motivo del fallo.');return;}
    var te=document.getElementById('f_type'),pe=document.getElementById('f_platform');
    document.getElementById('h_email').value=email;
    document.getElementById('h_type').value=te.value;
    document.getElementById('h_platform').value=pe.value||'';
    document.getElementById('h_reason').value=reason;
    document.getElementById('c_email').textContent=email;
    document.getElementById('c_type').textContent=te.options[te.selectedIndex].text;
    document.getElementById('c_platform').textContent=pe.value?pe.options[pe.selectedIndex].text:'N/A';
    document.getElementById('c_reason').textContent=reason;
    document.getElementById('step1').style.display='none';
    document.getElementById('step2').style.display='block';
}
function goStep1(){document.getElementById('step1').style.display='block';document.getElementById('step2').style.display='none';}
function updateChar(){var l=document.getElementById('f_reason').value.length,c=document.getElementById('charCount');c.textContent=l+' / 500';c.className='char-count'+(l>475?' danger':l>400?' warn':'');}

function openDetail(id,email,type,status,platform,reason,notes,newEmail,date){
    document.getElementById('d_date').textContent=date;
    document.getElementById('d_email').textContent=email;
    document.getElementById('d_platform').textContent=platform;
    document.getElementById('d_reason').textContent=reason;
    document.getElementById('d_status').innerHTML=sBadge[status]||status;
    document.getElementById('d_type').innerHTML=tBadge[type]||type;
    var nw=document.getElementById('d_new_email_wrap');
    if(newEmail){document.getElementById('d_new_email').textContent=newEmail;nw.style.display='block';}else{nw.style.display='none';}
    var no=document.getElementById('d_notes_wrap');
    if(notes){document.getElementById('d_notes').textContent=notes;no.style.display='block';}else{no.style.display='none';}
    document.getElementById('detailModal').classList.add('open');document.body.style.overflow='hidden';
}
function closeDetailModal(){document.getElementById('detailModal').classList.remove('open');document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeNewModal();closeDetailModal();}});
</script>
@endsection
