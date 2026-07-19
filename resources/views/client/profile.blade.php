@extends('client.layouts.app')
@section('title','Mi Perfil')
@section('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:"Poppins",sans-serif;box-sizing:border-box;}
.pro-page{max-width:900px;margin:0 auto;padding:2rem 1rem 6rem;position:relative;}
.orb{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0;}
.orb-1{width:500px;height:500px;background:rgba(124,58,237,0.1);top:-150px;left:-100px;}
.orb-2{width:400px;height:400px;background:rgba(236,72,153,0.07);top:300px;right:-150px;}

/* ALERTS */
.alert{display:flex;align-items:center;gap:.875rem;padding:1rem 1.5rem;border-radius:1rem;margin-bottom:1.5rem;font-size:.9rem;font-weight:600;backdrop-filter:blur(8px);position:relative;z-index:1;}
.alert-ok{background:rgba(52,211,153,.06);border:1px solid rgba(52,211,153,.3);color:#34d399;}
.alert-err{background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.3);color:#f87171;}

/* HERO SUMMARY */
.pro-hero{background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.08);border-top:2px solid rgba(168,85,247,.5);border-radius:1.5rem;padding:2rem 2.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;backdrop-filter:blur(12px);margin-bottom:1.5rem;position:relative;z-index:1;transition:border-color .3s;}
.pro-hero:hover{border-top-color:rgba(168,85,247,.8);}
.avatar{width:5rem;height:5rem;background:linear-gradient(135deg,rgba(124,58,237,.4),rgba(236,72,153,.25));border:2px solid rgba(168,85,247,.5);border-radius:1.25rem;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;color:white;flex-shrink:0;box-shadow:0 0 30px rgba(168,85,247,.2);}
.pro-name{font-size:1.625rem;font-weight:900;letter-spacing:-.03em;background:linear-gradient(135deg,#fff 0%,#c4b5fd 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.pro-email{font-size:.85rem;color:rgba(148,163,184,.65);margin-top:.2rem;}
.stats-row{display:flex;gap:1.5rem;flex-wrap:wrap;margin-top:1.25rem;}
.stat-pill{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:.875rem;padding:.625rem 1.125rem;display:flex;align-items:center;gap:.625rem;}
.stat-pill-val{font-size:1.05rem;font-weight:800;color:white;}
.stat-pill-lbl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(100,116,139,.8);}
.badge-active{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.3);color:#34d399;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:.25rem .625rem;border-radius:9999px;}
.badge-inactive{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:.25rem .625rem;border-radius:9999px;}

/* CARDS */
.pro-grid{display:grid;grid-template-columns:1fr;gap:1.25rem;position:relative;z-index:1;}
@media(min-width:768px){.pro-grid{grid-template-columns:1fr 1fr;}}
.pro-card{background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.07);border-radius:1.5rem;overflow:hidden;backdrop-filter:blur(12px);transition:border-color .3s;}
.pro-card:hover{border-color:rgba(168,85,247,.2);}
.pro-card.full{grid-column:1/-1;}
.card-hd{padding:1.25rem 1.75rem;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:.875rem;}
.card-hd-icon{width:2.25rem;height:2.25rem;border-radius:.625rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.card-hd-title{font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:rgba(168,85,247,.8);}
.card-bd{padding:1.5rem 1.75rem;}

/* INPUTS */
.f-label{display:block;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(148,163,184,.7);margin-bottom:.5rem;}
.f-input{width:100%;background:rgba(255,255,255,.03);border:1px solid rgba(168,85,247,.2);border-radius:.875rem;padding:.875rem 1.125rem;color:white;font-size:.9rem;transition:all .25s;outline:none;}
.f-input:focus{border-color:#a855f7;background:rgba(168,85,247,.05);box-shadow:0 0 0 3px rgba(168,85,247,.15);}
.f-input:disabled{opacity:.5;cursor:not-allowed;}
.f-group{margin-bottom:1.25rem;}
.f-group:last-child{margin-bottom:0;}
.pw-wrap{position:relative;}
.pw-wrap .f-input{padding-right:3rem;}
.pw-eye{position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(148,163,184,.5);cursor:pointer;padding:.25rem;transition:color .2s;}
.pw-eye:hover{color:#a855f7;}

/* PASSWORD STRENGTH */
.pw-strength{margin-top:.5rem;}
.pw-bars{display:flex;gap:.25rem;margin-bottom:.35rem;}
.pw-bar{height:3px;flex:1;border-radius:9999px;background:rgba(255,255,255,.08);transition:background .3s;}
.pw-bar.weak{background:#f87171;}
.pw-bar.fair{background:#fbbf24;}
.pw-bar.good{background:#34d399;}
.pw-bar.strong{background:#a855f7;}
.pw-label{font-size:.68rem;font-weight:700;color:rgba(100,116,139,.7);}
.pw-match-ok{font-size:.7rem;color:#34d399;margin-top:.35rem;display:none;}
.pw-match-err{font-size:.7rem;color:#f87171;margin-top:.35rem;display:none;}

/* BUTTONS */
.btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:.625rem;background:linear-gradient(135deg,#7c3aed,#a855f7,#ec4899);border:none;border-radius:.875rem;color:white;font-weight:800;font-size:.9rem;padding:.875rem 1.75rem;cursor:pointer;box-shadow:0 8px 25px rgba(168,85,247,.4);transition:all .3s;white-space:nowrap;width:100%;}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(168,85,247,.55);}
.btn-danger{display:inline-flex;align-items:center;justify-content:center;gap:.625rem;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:.875rem;color:#f87171;font-weight:800;font-size:.9rem;padding:.875rem 1.75rem;cursor:pointer;transition:all .3s;width:100%;}
.btn-danger:hover{background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.4);}
.btn-outline{display:inline-flex;align-items:center;justify-content:center;gap:.625rem;background:rgba(255,255,255,.03);border:1px dashed rgba(255,255,255,.15);border-radius:.875rem;color:rgba(148,163,184,.7);font-weight:700;font-size:.9rem;padding:.875rem 1.75rem;cursor:pointer;transition:all .2s;width:100%;}
.btn-outline:hover{background:rgba(255,255,255,.07);border-color:rgba(255,255,255,.3);color:white;border-style:solid;}

/* 2FA STATUS */
.twofa-on{background:rgba(52,211,153,.07);border:1px solid rgba(52,211,153,.3);border-radius:1rem;padding:1.25rem;display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;}
.twofa-off{background:rgba(251,191,36,.05);border:1px solid rgba(251,191,36,.25);border-radius:1rem;padding:1.25rem;display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;}
.twofa-icon{width:2.75rem;height:2.75rem;border-radius:.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}

/* MODAL */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.82);backdrop-filter:blur(14px);z-index:9999;align-items:center;justify-content:center;padding:1rem;}
.modal-bg.open{display:flex;}
.modal-box{background:linear-gradient(145deg,rgba(18,10,45,.99) 0%,rgba(8,4,20,1) 100%);border:1px solid rgba(168,85,247,.3);border-radius:1.5rem;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 30px 80px rgba(0,0,0,.8),0 0 60px rgba(168,85,247,.1);position:relative;animation:modalIn .35s cubic-bezier(.16,1,.3,1) both;}
@keyframes modalIn{from{transform:translateY(24px) scale(.96);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#7c3aed,#a855f7,#ec4899,transparent);}
.modal-hd{padding:1.75rem 2rem 1.25rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;}
.modal-bd{padding:0 2rem 1.5rem;}
.modal-ft{padding:1.25rem 2rem;border-top:1px solid rgba(255,255,255,.06);display:flex;justify-content:flex-end;gap:.875rem;}
.modal-close{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.625rem;color:rgba(148,163,184,.6);width:2.25rem;height:2.25rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;flex-shrink:0;}
.modal-close:hover{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.3);color:#f87171;}

/* QR AREA */
.qr-wrap{background:white;border-radius:1rem;padding:1rem;display:inline-block;margin:1rem auto;display:flex;justify-content:center;}
.qr-wrap svg{width:220px;height:220px;}
.secret-box{background:rgba(168,85,247,.07);border:1px solid rgba(168,85,247,.2);border-radius:.875rem;padding:.875rem 1rem;text-align:center;font-family:monospace;font-size:.9rem;color:#c4b5fd;letter-spacing:.15em;margin-bottom:1.25rem;word-break:break-all;}
.step-pill{display:inline-flex;align-items:center;gap:.5rem;background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.25);color:#c4b5fd;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:.3rem .75rem;border-radius:9999px;margin-bottom:.875rem;}
.info-note{background:rgba(168,85,247,.07);border:1px solid rgba(168,85,247,.2);border-radius:.875rem;padding:.875rem 1rem;font-size:.8rem;color:rgba(196,181,253,.85);display:flex;align-items:flex-start;gap:.625rem;margin-bottom:1.25rem;}

/* LAST LOGIN */
.last-login{font-size:.75rem;color:rgba(100,116,139,.7);margin-top:.35rem;display:flex;align-items:center;gap:.375rem;}

@media(max-width:640px){
    .pro-page{padding:1.5rem 0.5rem 6rem;}
    .pro-hero{padding:1.25rem;flex-direction:column;align-items:flex-start;}
    .card-bd{padding:1.25rem 1rem;}
    .card-hd{padding:1rem;}
}
</style>
@endsection

@section('content')
@php
    $totalCuentas    = $client->allowedEmails()->count();
    $garantiasActivas = $client->warrantyRequests()->whereIn('status',['pending','approved'])->count();
    $has2fa          = $client->two_factor_secret && $client->two_factor_confirmed_at;
    $siteName        = \App\Models\Setting::get('site_name','NexusCode');
@endphp

<div class="pro-page">
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

{{-- FLASH --}}
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
@if ($errors->any())
<div class="alert alert-err">
    <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
</div>
@endif

{{-- HERO --}}
<div class="pro-hero">
    {{-- Avatar clickeable --}}
    <div style="position:relative;flex-shrink:0;">
        <div id="avatarDisplay" onclick="document.getElementById('avatarInput').click()" style="width:5.5rem;height:5.5rem;border-radius:1.25rem;cursor:pointer;overflow:hidden;border:2px solid rgba(168,85,247,.4);box-shadow:0 0 30px rgba(168,85,247,.2);position:relative;background:linear-gradient(135deg,rgba(124,58,237,.3),rgba(236,72,153,.2));display:flex;align-items:center;justify-content:center;transition:border-color .2s;" onmouseover="document.getElementById('avatarOverlay').style.opacity='1'" onmouseout="document.getElementById('avatarOverlay').style.opacity='0'">
            @if($client->avatar)
                <img id="avatarImg" src="{{ asset($client->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="Avatar">
            @else
                <span id="avatarInitial" style="font-size:2.2rem;font-weight:900;color:white;">{{ strtoupper(substr($client->name,0,1)) }}</span>
            @endif
            {{-- Overlay hover --}}
            <div id="avatarOverlay" style="position:absolute;inset:0;background:rgba(0,0,0,.6);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.25rem;opacity:0;transition:opacity .2s;border-radius:1.2rem;">
                <svg style="width:1.25rem;height:1.25rem;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span style="font-size:.6rem;font-weight:800;color:rgba(255,255,255,.8);text-transform:uppercase;letter-spacing:.06em;">Cambiar</span>
            </div>
        </div>
    </div>
    <div style="flex:1;">
        <div style="display:flex;align-items:center;gap:.875rem;flex-wrap:wrap;">
            <h1 class="pro-name">{{ $client->name }}</h1>
            @if($client->is_active)<span class="badge-active">Activo</span>@else<span class="badge-inactive">Inactivo</span>@endif
            @if($has2fa)<span style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.3);color:#34d399;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:.25rem .625rem;border-radius:9999px;display:inline-flex;align-items:center;gap:.3rem;"><svg style="width:.7rem;height:.7rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>2FA</span>@endif
        </div>
        <p class="pro-email">{{ $client->email }}</p>
        @if($client->pending_email)
        <p style="font-size:.72rem;color:rgba(251,191,36,.7);margin-top:.25rem;display:flex;align-items:center;gap:.35rem;">
            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Cambio pendiente → {{ $client->pending_email }} (verifica tu bandeja)
        </p>
        @endif
        @if(isset($client->last_login_at) && $client->last_login_at)
        <p class="last-login">
            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Último acceso: {{ $client->last_login_at->diffForHumans() }}
        </p>
        @else
        <p class="last-login">
            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Miembro desde: {{ $client->created_at->format('d/m/Y') }}
        </p>
        @endif
        <div class="stats-row">
            <div class="stat-pill">
                <div style="width:1.75rem;height:1.75rem;background:rgba(168,85,247,.15);border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:.9rem;height:.9rem;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div><div class="stat-pill-val">{{ $totalCuentas }}</div><div class="stat-pill-lbl">Cuentas</div></div>
            </div>
            <div class="stat-pill">
                <div style="width:1.75rem;height:1.75rem;background:rgba(251,191,36,.12);border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:.9rem;height:.9rem;color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div><div class="stat-pill-val">{{ $garantiasActivas }}</div><div class="stat-pill-lbl">Garantías</div></div>
            </div>
            <div class="stat-pill">
                <div style="width:1.75rem;height:1.75rem;background:rgba(52,211,153,.12);border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:.9rem;height:.9rem;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><div class="stat-pill-val">{{ $client->created_at->format('d/m/Y') }}</div><div class="stat-pill-lbl">Registro</div></div>
            </div>
        </div>
    </div>
</div>

{{-- AVATAR UPLOAD FORM (hidden) --}}
<form id="avatarForm" action="{{ route('client.profile.avatar') }}" method="POST" enctype="multipart/form-data" style="display:none;">
    @csrf
    <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="previewAvatar(event)">
</form>

<div class="pro-grid">

    {{-- INFO PERSONAL --}}
    <div class="pro-card">
        <div class="card-hd">
            <div class="card-hd-icon" style="background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.2);">
                <svg style="width:1rem;height:1rem;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="card-hd-title">Información Personal</span>
        </div>
        <div class="card-bd">
            <form action="{{ route('client.profile.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="f-group">
                    <label class="f-label">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name',$client->name) }}" class="f-input" required>
                </div>
                <div class="f-group">
                    <label class="f-label">Teléfono / WhatsApp <span style="color:rgba(100,116,139,.5);">(Opcional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone',$client->phone ?? '') }}" class="f-input" placeholder="+1 555 0000">
                </div>
                <button type="submit" class="btn-primary" style="margin-top:.5rem;">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>

    {{-- CAMBIO DE EMAIL --}}
    <div class="pro-card">
        <div class="card-hd">
            <div class="card-hd-icon" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);">
                <svg style="width:1rem;height:1rem;color:#60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <span class="card-hd-title">Correo Electrónico</span>
        </div>
        <div class="card-bd">
            <div class="f-group">
                <label class="f-label">Correo Actual</label>
                <input type="email" value="{{ $client->email }}" class="f-input" disabled>
            </div>
            @if(session('email_change_sent'))
            <div class="alert alert-ok" style="margin-bottom:1rem;">
                <svg style="width:1.1rem;height:1.1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('email_change_sent') }}
            </div>
            @endif
            <form action="{{ route('client.profile.request-email-change') }}" method="POST">
                @csrf
                <div class="f-group">
                    <label class="f-label">Nuevo Correo Electrónico</label>
                    <input type="email" name="new_email" value="{{ old('new_email') }}" class="f-input" placeholder="nuevo@email.com" required>
                    @error('new_email')<p style="color:#f87171;font-size:.75rem;margin-top:.35rem;">{{ $message }}</p>@enderror
                </div>
                <div class="info-note" style="margin-bottom:1rem;">
                    <svg style="width:.9rem;height:.9rem;flex-shrink:0;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Se enviará un enlace de verificación al <strong>nuevo correo</strong>. El cambio solo aplicará al confirmar el enlace (válido por 60 min).
                </div>
                <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6,#06b6d4);box-shadow:0 8px 25px rgba(59,130,246,.35);">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Solicitar Cambio de Correo
                </button>
            </form>
        </div>
    </div>

    {{-- SEGURIDAD - CONTRASEÑA --}}
    <div class="pro-card">
        <div class="card-hd">
            <div class="card-hd-icon" style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.2);">
                <svg style="width:1rem;height:1rem;color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <span class="card-hd-title">Cambiar Contraseña</span>
        </div>
        <div class="card-bd">
            <form action="{{ route('client.profile.update') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="name" value="{{ $client->name }}">
                <input type="hidden" name="phone" value="{{ $client->phone ?? '' }}">
                <div class="f-group">
                    <label class="f-label">Nueva Contraseña</label>
                    <div class="pw-wrap">
                        <input type="password" name="password" id="pwField" class="f-input" placeholder="Mínimo 8 caracteres" oninput="checkStrength(this.value)">
                        <button type="button" class="pw-eye" onclick="togglePw('pwField','eyeIcon1')">
                            <svg id="eyeIcon1" style="width:1.1rem;height:1.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <div class="pw-strength" id="pwStrength" style="display:none;">
                        <div class="pw-bars">
                            <div class="pw-bar" id="bar1"></div>
                            <div class="pw-bar" id="bar2"></div>
                            <div class="pw-bar" id="bar3"></div>
                            <div class="pw-bar" id="bar4"></div>
                        </div>
                        <span class="pw-label" id="pwLabel">Muy débil</span>
                    </div>
                </div>
                <div class="f-group">
                    <label class="f-label">Confirmar Contraseña</label>
                    <div class="pw-wrap">
                        <input type="password" name="password_confirmation" id="pwConfirm" class="f-input" placeholder="Repite la contraseña" oninput="checkMatch()">
                        <button type="button" class="pw-eye" onclick="togglePw('pwConfirm','eyeIcon2')">
                            <svg id="eyeIcon2" style="width:1.1rem;height:1.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <div class="pw-match-ok" id="matchOk">✓ Las contraseñas coinciden</div>
                    <div class="pw-match-err" id="matchErr">✗ Las contraseñas no coinciden</div>
                </div>
                <button type="submit" class="btn-primary" id="pwSubmitBtn" style="margin-top:.5rem;" onclick="return validatePw()">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Actualizar Contraseña
                </button>
            </form>
        </div>
    </div>

    {{-- 2FA - GOOGLE AUTHENTICATOR --}}
    @if(\App\Models\Setting::get('enable_2fa', true))
    <div class="pro-card">
        <div class="card-hd">
            <div class="card-hd-icon" style="background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.2);">
                <svg style="width:1rem;height:1rem;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="card-hd-title">Autenticación de Dos Factores (2FA)</span>
        </div>
        <div class="card-bd">
            @if($has2fa)
            <div class="twofa-on">
                <div class="twofa-icon" style="background:rgba(52,211,153,.15);border:1px solid rgba(52,211,153,.3);">
                    <svg style="width:1.25rem;height:1.25rem;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div style="flex:1;">
                    <p style="font-size:.9rem;font-weight:800;color:#34d399;">Protección Activa</p>
                    <p style="font-size:.78rem;color:rgba(52,211,153,.7);margin-top:.15rem;">Tu cuenta está protegida con Google Authenticator. Se pedirá un código al iniciar sesión.</p>
                </div>
            </div>
            <div style="max-width:300px;">
                <button type="button" class="btn-danger" onclick="disable2FA()">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Desactivar 2FA
                </button>
            </div>
            @else
            <div class="twofa-off">
                <div class="twofa-icon" style="background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);">
                    <svg style="width:1.25rem;height:1.25rem;color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div style="flex:1;">
                    <p style="font-size:.9rem;font-weight:800;color:#fbbf24;">2FA Desactivado</p>
                    <p style="font-size:.78rem;color:rgba(251,191,36,.7);margin-top:.15rem;">Activa Google Authenticator para proteger tu cuenta con una capa de seguridad extra.</p>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div>
                    <p style="font-size:.82rem;color:rgba(148,163,184,.7);margin-bottom:.875rem;line-height:1.5;">
                        <strong style="color:rgba(196,181,253,.9);">¿Cómo funciona?</strong><br>
                        Descarga <strong>Google Authenticator</strong> en tu celular, escanea el código QR que aparecerá y confirma con el código de 6 dígitos.
                    </p>
                </div>
                <div style="display:flex;flex-direction:column;gap:.625rem;">
                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" style="display:flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:.75rem;padding:.625rem .875rem;color:rgba(148,163,184,.7);font-size:.75rem;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.07)'" onmouseout="this.style.background='rgba(255,255,255,.03)'">
                        <svg style="width:.875rem;height:.875rem;color:#34d399;" fill="currentColor" viewBox="0 0 24 24"><path d="M3.18 23.76A1.52 1.52 0 012 22.33V1.67A1.52 1.52 0 013.18.24l12.13 11.76zM17.82 13.6L5.88 20.34l9.61-9.31zM20.44 10.56a1.49 1.49 0 010 2.88l-2.62 1.51-3-2.94 3-2.96zM5.88 3.66l11.94 6.74-3 2.92z"/></svg>
                        Android
                    </a>
                    <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank" style="display:flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:.75rem;padding:.625rem .875rem;color:rgba(148,163,184,.7);font-size:.75rem;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.07)'" onmouseout="this.style.background='rgba(255,255,255,.03)'">
                        <svg style="width:.875rem;height:.875rem;color:rgba(148,163,184,.7);" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                        iOS
                    </a>
                </div>
            </div>
            <div style="margin-top:1.25rem;">
                <button type="button" class="btn-primary" style="max-width:300px;" onclick="setup2FA()">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Activar Google Authenticator
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
</div>

{{-- MODAL 2FA SETUP --}}
<div id="modal2fa" class="modal-bg" onclick="if(event.target===this)closeModal2fa()">
    <div class="modal-box">
        <div class="modal-hd">
            <div style="display:flex;align-items:center;gap:.875rem;">
                <div style="width:2.75rem;height:2.75rem;background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.3);border-radius:.875rem;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:1.25rem;height:1.25rem;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h3 style="font-size:1.05rem;font-weight:800;color:white;">Activar Google 2FA</h3>
                    <p style="font-size:.72rem;color:rgba(148,163,184,.5);">Protege tu cuenta con código único</p>
                </div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal2fa()"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="modal-bd">
            {{-- Loading --}}
            <div id="twofa-loading" style="text-align:center;padding:3rem 0;">
                <div style="width:3rem;height:3rem;border:3px solid rgba(168,85,247,.2);border-top:3px solid #a855f7;border-radius:50%;animation:spin .8s linear infinite;margin:0 auto 1rem;"></div>
                <p style="color:rgba(148,163,184,.6);font-size:.85rem;">Generando código QR...</p>
            </div>
            {{-- QR Content --}}
            <div id="twofa-content" style="display:none;">
                <div class="step-pill">Paso 1 — Escanear QR</div>
                <div class="info-note">
                    <svg style="width:.9rem;height:.9rem;flex-shrink:0;margin-top:1px;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Abre <strong>Google Authenticator</strong> en tu celular, toca el <strong>+</strong> y escanea este código QR.
                </div>
                <div class="qr-wrap" id="qrCodeContainer"></div>
                <p style="font-size:.72rem;text-align:center;color:rgba(100,116,139,.7);margin:.5rem 0 1rem;">¿No puedes escanear? Ingresa este código manualmente:</p>
                <div class="secret-box" id="secretKey"></div>
                <div class="step-pill">Paso 2 — Confirmar</div>
                <div class="f-group" style="margin-bottom:0;">
                    <label class="f-label">Código de 6 dígitos de la app</label>
                    <input type="text" id="twofa-code" class="f-input" placeholder="000 000" maxlength="6" style="text-align:center;font-size:1.25rem;letter-spacing:.3em;font-family:monospace;" oninput="this.value=this.value.replace(/\D/g,'')">
                    <p id="twofa-error" style="color:#f87171;font-size:.75rem;margin-top:.5rem;display:none;"></p>
                </div>
            </div>
        </div>
        <div class="modal-ft" id="twofa-footer" style="display:none;">
            <button type="button" class="btn-outline" style="width:auto;" onclick="closeModal2fa()">Cancelar</button>
            <button type="button" class="btn-primary" style="width:auto;" onclick="confirm2FA()">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Confirmar y Activar
            </button>
        </div>
    </div>
</div>

<style>@keyframes spin{to{transform:rotate(360deg)}}</style>

<script>
/* AVATAR PREVIEW */
function previewAvatar(event) {
    var file = event.target.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var display = document.getElementById('avatarDisplay');
        // Reemplazar contenido con la imagen preview
        display.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;" alt="Preview">' +
            '<div id="avatarOverlay" style="position:absolute;inset:0;background:rgba(0,0,0,.5);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.25rem;border-radius:1.2rem;">' +
            '<svg style="width:1.25rem;height:1.25rem;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
            '<span style="font-size:.6rem;font-weight:800;color:rgba(255,255,255,.9);text-transform:uppercase;letter-spacing:.06em;">Guardando...</span>' +
            '</div>';
        // Auto-submit el form
        document.getElementById('avatarForm').submit();
    };
    reader.readAsDataURL(file);
}
function togglePw(inputId, iconId) {
    var inp = document.getElementById(inputId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
}

/* ── PASSWORD STRENGTH ── */
function checkStrength(val) {
    var el = document.getElementById('pwStrength');
    var lbl = document.getElementById('pwLabel');
    var bars = [document.getElementById('bar1'),document.getElementById('bar2'),document.getElementById('bar3'),document.getElementById('bar4')];
    if (!val) { el.style.display='none'; return; }
    el.style.display='block';
    bars.forEach(b=>{ b.className='pw-bar'; });
    var score = 0;
    if (val.length >= 8) score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;
    var labels = ['Muy débil','Débil','Media','Fuerte'];
    var cls    = ['weak','weak','fair','good','strong'];
    lbl.textContent = labels[Math.min(score,3)];
    lbl.style.color = ['#f87171','#f87171','#fbbf24','#34d399','#a855f7'][score];
    for (var i=0; i<=score && i<4; i++) bars[i].className='pw-bar '+cls[score];
}

/* ── PASSWORD MATCH ── */
function checkMatch() {
    var pw  = document.getElementById('pwField').value;
    var cpw = document.getElementById('pwConfirm').value;
    var ok  = document.getElementById('matchOk');
    var err = document.getElementById('matchErr');
    if (!cpw) { ok.style.display='none'; err.style.display='none'; return; }
    if (pw === cpw) { ok.style.display='block'; err.style.display='none'; }
    else            { ok.style.display='none'; err.style.display='block'; }
}

function validatePw() {
    var pw  = document.getElementById('pwField').value;
    var cpw = document.getElementById('pwConfirm').value;
    if (pw && pw !== cpw) { alert('Las contraseñas no coinciden.'); return false; }
    return true;
}

/* ── 2FA SETUP ── */
function setup2FA() {
    document.getElementById('modal2fa').classList.add('open');
    document.body.style.overflow = 'hidden';
    document.getElementById('twofa-loading').style.display = 'block';
    document.getElementById('twofa-content').style.display = 'none';
    document.getElementById('twofa-footer').style.display  = 'none';

    fetch('{{ route("client.2fa.enable") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('qrCodeContainer').innerHTML = data.qrCode;
            document.getElementById('secretKey').textContent = data.secret;
            document.getElementById('twofa-loading').style.display = 'none';
            document.getElementById('twofa-content').style.display  = 'block';
            document.getElementById('twofa-footer').style.display   = 'flex';
        }
    })
    .catch(() => {
        document.getElementById('twofa-loading').innerHTML = '<p style="color:#f87171;">Error generando QR. Recarga la página.</p>';
    });
}

function confirm2FA() {
    var code = document.getElementById('twofa-code').value.trim();
    var errEl = document.getElementById('twofa-error');
    if (code.length !== 6) { errEl.textContent='Ingresa los 6 dígitos.'; errEl.style.display='block'; return; }
    errEl.style.display='none';

    fetch('{{ route("client.2fa.confirm") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: code })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { location.reload(); }
        else { errEl.textContent = data.message || 'Código incorrecto.'; errEl.style.display='block'; }
    })
    .catch(() => { errEl.textContent='Error de conexión.'; errEl.style.display='block'; });
}

function disable2FA() {
    if (!confirm('¿Desactivar la autenticación de dos factores? Tu cuenta quedará menos segura.')) return;
    fetch('{{ route("client.2fa.disable") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(() => alert('Error al desactivar. Intenta de nuevo.'));
}

function closeModal2fa() {
    document.getElementById('modal2fa').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key==='Escape') closeModal2fa(); });
</script>
@endsection
