@extends('client.layouts.app')

@section('title', 'Acerca de Tu Codigo')

@section('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

* { font-family: "Poppins", sans-serif; }

/* PAGE WRAPPER */
.about-page {
    min-height: 100vh;
    padding: 2rem 1rem 8rem;
    position: relative;
    overflow-x: hidden;
}

/* AMBIENT ORBS */
.orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
}
.orb-1 { width: 500px; height: 500px; background: rgba(124,58,237,0.12); top: -150px; left: -100px; }
.orb-2 { width: 400px; height: 400px; background: rgba(236,72,153,0.08); top: 200px; right: -150px; }
.orb-3 { width: 350px; height: 350px; background: rgba(59,130,246,0.07); bottom: 0; left: 50%; }

.about-inner {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* ─── HERO ─── */
.hero-section {
    text-align: center;
    padding: 4rem 2rem 3.5rem;
    margin-bottom: 3rem;
    position: relative;
}

.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(124,58,237,0.12);
    border: 1px solid rgba(124,58,237,0.35);
    color: #a78bfa;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 0.5rem 1.25rem;
    border-radius: 9999px;
    margin-bottom: 2rem;
}
.eyebrow-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #4ade80;
    box-shadow: 0 0 8px #4ade80;
    animation: dotPulse 1.8s ease-in-out infinite;
}
@keyframes dotPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }

.hero-icon {
    width: 7rem; height: 7rem;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 40%, #9333ea 70%, #ec4899 100%);
    border-radius: 2rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow:
        0 0 0 1px rgba(255,255,255,0.08) inset,
        0 20px 60px rgba(124,58,237,0.5),
        0 0 120px rgba(124,58,237,0.2);
    animation: iconFloat 3.5s ease-in-out infinite;
    position: relative;
}
.hero-icon::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 2.25rem;
    background: linear-gradient(135deg, rgba(124,58,237,0.5), rgba(236,72,153,0.3));
    z-index: -1;
    filter: blur(10px);
}
@keyframes iconFloat {
    0%,100% { transform: translateY(0) rotate(0deg); }
    33%      { transform: translateY(-8px) rotate(-1deg); }
    66%      { transform: translateY(-4px) rotate(1deg); }
}

.hero-title {
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -0.03em;
    background: linear-gradient(135deg, #fff 0%, #e0d7ff 40%, #c4b5fd 70%, #a78bfa 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.hero-subtitle {
    font-size: 1.15rem;
    color: rgba(148,163,184,0.75);
    font-weight: 400;
    letter-spacing: -0.01em;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.version-chip {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(15,12,40,0.8);
    border: 1px solid rgba(255,255,255,0.12);
    color: rgba(203,213,225,0.8);
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: 9999px;
    backdrop-filter: blur(8px);
}
.version-chip span { color: #4ade80; font-weight: 800; }

/* ─── SECTION DIVIDER ─── */
.section-divider {
    display: flex; align-items: center; gap: 1rem;
    margin: 3rem 0 2rem;
}
.section-divider::before, .section-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(124,58,237,0.3), transparent);
}
.section-divider-label {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: rgba(124,58,237,0.7);
    white-space: nowrap;
}

/* ─── GLASS CARDS ─── */
.glass {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 1.5rem;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    position: relative;
    transition: border-color 0.35s, transform 0.35s, box-shadow 0.35s;
}
.glass::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: linear-gradient(145deg, rgba(255,255,255,0.04) 0%, transparent 50%);
    pointer-events: none;
}
.glass:hover {
    border-color: rgba(124,58,237,0.45);
    transform: translateY(-4px);
    box-shadow: 0 30px 60px -15px rgba(0,0,0,0.6), 0 0 0 1px rgba(124,58,237,0.15), 0 0 60px rgba(124,58,237,0.08);
}

/* highlight top border on hover */
.glass-purple { border-top: 1.5px solid rgba(124,58,237,0.3); }
.glass-green  { border-top: 1.5px solid rgba(52,211,153,0.3); }
.glass-blue   { border-top: 1.5px solid rgba(59,130,246,0.3); }
.glass-pink   { border-top: 1.5px solid rgba(236,72,153,0.3); }

/* ─── 2-COL GRID ─── */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
@media (max-width: 640px) { .grid-2 { grid-template-columns: 1fr; } }

/* ─── CARD HEADER ─── */
.card-tag {
    display: inline-flex; align-items: center; gap: 0.5rem;
    font-size: 0.68rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.12em;
    margin-bottom: 0.75rem;
}
.card-tag-icon {
    width: 1.75rem; height: 1.75rem;
    border-radius: 0.5rem;
    display: flex; align-items: center; justify-content: center;
}
.card-title {
    font-size: 1.25rem; font-weight: 800;
    color: white; letter-spacing: -0.02em;
    margin-bottom: 1rem;
}
.card-text {
    font-size: 0.9rem; line-height: 1.75;
    color: rgba(148,163,184,0.8);
}
.card-text strong { color: rgba(216,180,254,0.9); font-weight: 600; }

/* ─── STEPS ─── */
.steps { display: flex; flex-direction: column; gap: 0.875rem; }
.step {
    display: flex; align-items: flex-start; gap: 1rem;
    padding: 0.875rem;
    border-radius: 1rem;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    transition: background 0.2s, border-color 0.2s;
}
.step:hover {
    background: rgba(124,58,237,0.06);
    border-color: rgba(124,58,237,0.2);
}
.step-badge {
    min-width: 2rem; height: 2rem;
    background: linear-gradient(135deg, #7c3aed, #9333ea);
    border-radius: 0.625rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 900; color: white;
    box-shadow: 0 4px 12px rgba(124,58,237,0.4);
}
.step-text { font-size: 0.875rem; color: rgba(148,163,184,0.85); line-height: 1.6; }

/* ─── FEATURE GRID ─── */
.feat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; }
@media(max-width:680px){ .feat-grid{ grid-template-columns: 1fr 1fr; } }
@media(max-width:420px){ .feat-grid{ grid-template-columns: 1fr; } }

.feat-tile {
    padding: 1.25rem;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.07);
    transition: all 0.3s;
    cursor: default;
}
.feat-tile:hover {
    background: rgba(124,58,237,0.08);
    border-color: rgba(124,58,237,0.35);
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(124,58,237,0.12);
}
.feat-tile-icon {
    width: 2.75rem; height: 2.75rem;
    border-radius: 0.875rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.875rem;
    transition: transform 0.3s;
}
.feat-tile:hover .feat-tile-icon { transform: scale(1.1) rotate(-3deg); }
.feat-tile-title { font-size: 0.875rem; font-weight: 700; color: white; margin-bottom: 0.35rem; }
.feat-tile-desc  { font-size: 0.775rem; line-height: 1.55; color: rgba(100,116,139,0.9); }

/* ─── SUPPORT SECTION ─── */
.support-section {
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.08);
    border-top: 2px solid rgba(52,211,153,0.4);
    border-radius: 1.5rem;
    padding: 3.5rem 2rem;
    text-align: center;
    backdrop-filter: blur(12px);
    position: relative;
    overflow: hidden;
    margin-top: 1.25rem;
    transition: border-color 0.3s;
}
.support-section::before {
    content: '';
    position: absolute;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(52,211,153,0.07) 0%, transparent 70%);
    top: -100px; left: 50%; transform: translateX(-50%);
    pointer-events: none;
}
.support-title {
    font-size: 2rem; font-weight: 900;
    background: linear-gradient(135deg, #fff 0%, #86efac 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    margin-bottom: 0.625rem;
}
.support-sub { color: rgba(148,163,184,0.65); font-size: 0.9rem; margin-bottom: 2rem; }

.btn-contact {
    display: inline-flex; align-items: center; gap: 0.625rem;
    padding: 0.9rem 1.75rem;
    border-radius: 0.875rem;
    font-size: 0.875rem; font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    border: 1px solid;
}
.btn-tg  { background: rgba(38,165,228,0.08); border-color: rgba(38,165,228,0.3); color: #38bdf8; }
.btn-tg:hover  { background: rgba(38,165,228,0.18); border-color: rgba(38,165,228,0.6); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(38,165,228,0.18); }
.btn-wa  { background: rgba(37,211,102,0.08); border-color: rgba(37,211,102,0.3); color: #4ade80; }
.btn-wa:hover  { background: rgba(37,211,102,0.18); border-color: rgba(37,211,102,0.6); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(37,211,102,0.18); }

.footer-copy { font-size: 0.75rem; color: rgba(71,85,105,0.8); margin-top: 2.5rem; }
</style>
@endsection

@section('content')
@php
    $authClient = auth('client')->user();
    $user       = $authClient?->user;
    $telegram   = ($user && $user->telegram) ? $user->telegram : \App\Models\Setting::get('telegram_url');
    $whatsapp   = ($user && $user->whatsapp) ? $user->whatsapp : \App\Models\Setting::get('whatsapp_url');
    $siteName   = \App\Models\Setting::get('site_name', 'Tu Codigo');
@endphp

<div class="about-page">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="about-inner">

        {{-- ── HERO ── --}}
        <div class="hero-section">
            <div class="hero-eyebrow">
                <span class="eyebrow-dot"></span>
                Version 2.0 &nbsp;&mdash;&nbsp; Produccion Estable
            </div>
            <div class="hero-icon">
                <svg style="width:3rem;height:3rem;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="hero-title">{{ $siteName }}</h1>
            <p class="hero-subtitle">Sistema Profesional de Verificacion de Codigos<br>en Tiempo Real para Streaming y Servicios Digitales.</p>
            <div class="version-chip">
                <svg style="width:0.875rem;height:0.875rem;color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Produccion &nbsp;<span>Activa</span>
            </div>
        </div>

        {{-- ── QUE ES / COMO FUNCIONA ── --}}
        <div class="section-divider"><span class="section-divider-label">Plataforma</span></div>
        <div class="grid-2" style="margin-bottom:1.25rem;">

            <div class="glass glass-purple" style="padding:2rem;">
                <div class="card-tag" style="color:#a78bfa;">
                    <div class="card-tag-icon" style="background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.3);">
                        <svg style="width:0.875rem;height:0.875rem;color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    Que es
                </div>
                <h2 class="card-title">{{ $siteName }}</h2>
                <p class="card-text">
                    <strong>{{ $siteName }}</strong> es un sistema automatizado que extrae en tiempo real los codigos de verificacion enviados a cuentas de correo corporativo, permitiendo acceder a plataformas de streaming y servicios digitales de forma <strong>rapida y segura</strong>.
                </p>
            </div>

            <div class="glass glass-blue" style="padding:2rem;">
                <div class="card-tag" style="color:#60a5fa;">
                    <div class="card-tag-icon" style="background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.25);">
                        <svg style="width:0.875rem;height:0.875rem;color:#60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    Como funciona
                </div>
                <h2 class="card-title">Proceso Simple</h2>
                <div class="steps">
                    @foreach([
                        'Seleccionas la plataforma que necesitas.',
                        'El sistema se conecta al correo corporativo.',
                        'Extrae el codigo de verificacion al instante.',
                        'El codigo expira automaticamente por seguridad.',
                    ] as $i => $step)
                    <div class="step">
                        <div class="step-badge">{{ $i + 1 }}</div>
                        <p class="step-text">{{ $step }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── CARACTERISTICAS ── --}}
        <div class="section-divider"><span class="section-divider-label">Funcionalidades</span></div>
        <div class="glass glass-pink" style="padding:2rem;margin-bottom:0;">
            <div class="card-tag" style="color:#f472b6;margin-bottom:1.5rem;">
                <div class="card-tag-icon" style="background:rgba(236,72,153,0.12);border:1px solid rgba(236,72,153,0.25);">
                    <svg style="width:0.875rem;height:0.875rem;color:#f472b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                Caracteristicas Principales
            </div>
            @php
            $features = [
                ['bg'=>'rgba(251,191,36,0.12)','border'=>'rgba(251,191,36,0.25)','ic'=>'#fbbf24','path'=>'M13 10V3L4 14h7v7l9-11h-7z','t'=>'Ultra Rapido','d'=>'Extraccion de codigos en segundos mediante IMAP seguro.'],
                ['bg'=>'rgba(52,211,153,0.12)','border'=>'rgba(52,211,153,0.25)','ic'=>'#34d399','path'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','t'=>'Seguro y Privado','d'=>'Conexion cifrada. Los codigos no se almacenan permanentemente.'],
                ['bg'=>'rgba(59,130,246,0.12)','border'=>'rgba(59,130,246,0.25)','ic'=>'#60a5fa','path'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','t'=>'Multi-Plataforma','d'=>'Compatible con Netflix, Spotify, Prime, Max y mas.'],
                ['bg'=>'rgba(168,85,247,0.12)','border'=>'rgba(168,85,247,0.25)','ic'=>'#a855f7','path'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','t'=>'Control de Limites','d'=>'El administrador gestiona las consultas de cada usuario.'],
                ['bg'=>'rgba(239,68,68,0.12)','border'=>'rgba(239,68,68,0.25)','ic'=>'#f87171','path'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','t'=>'Garantias','d'=>'Sistema que pausa el tiempo al reportar fallas.'],
                ['bg'=>'rgba(236,72,153,0.12)','border'=>'rgba(236,72,153,0.25)','ic'=>'#f472b6','path'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','t'=>'Disponible 24/7','d'=>'Sin horarios de atencion. El sistema opera las 24 horas.'],
            ];
            @endphp
            <div class="feat-grid">
                @foreach($features as $f)
                <div class="feat-tile">
                    <div class="feat-tile-icon" style="background:{{ $f['bg'] }};border:1px solid {{ $f['border'] }};">
                        <svg style="width:1.25rem;height:1.25rem;color:{{ $f['ic'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['path'] }}"/>
                        </svg>
                    </div>
                    <p class="feat-tile-title">{{ $f['t'] }}</p>
                    <p class="feat-tile-desc">{{ $f['d'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── SOPORTE ── --}}
        <div class="section-divider"><span class="section-divider-label">Soporte</span></div>
        <div class="support-section">
            <div style="display:inline-flex;align-items:center;justify-content:center;width:3.5rem;height:3.5rem;background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.3);border-radius:1rem;margin-bottom:1.25rem;">
                <svg style="width:1.75rem;height:1.75rem;color:#4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h2 class="support-title">Necesitas ayuda?</h2>
            <p class="support-sub">Nuestro equipo de soporte esta disponible para asistirte en cualquier momento.</p>
            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:1rem;">
                @if($telegram)
                <a href="{{ $telegram }}" target="_blank" class="btn-contact btn-tg">
                    <svg style="width:1rem;height:1rem;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.696.064-1.225-.46-1.901-.903-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    Soporte Telegram
                </a>
                @endif
                @if($whatsapp)
                <a href="{{ $whatsapp }}" target="_blank" class="btn-contact btn-wa">
                    <svg style="width:1rem;height:1rem;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Soporte WhatsApp
                </a>
                @endif
            </div>
            <p class="footer-copy">&copy; {{ date('Y') }} {{ $siteName }} &mdash; Todos los derechos reservados.</p>
        </div>

    </div>
</div>
@endsection
