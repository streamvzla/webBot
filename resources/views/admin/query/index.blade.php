@extends('admin.layouts.app')

@section('title', 'Consultar Código')

@section('header', 'Consultar Código de Verificación')

@section('styles')
<style>
    /* ===== DESIGN SYSTEM UNIFICADO ===== */

    /* Orbs de fondo — idénticos a warranties y profile */
    .orb { position:fixed; border-radius:50%; filter:blur(80px); pointer-events:none; z-index:0; }
    .orb-1 { width:500px; height:500px; background:rgba(124,58,237,0.1); top:-150px; left:-100px; }
    .orb-2 { width:400px; height:400px; background:rgba(236,72,153,0.07); top:300px; right:-150px; }

    .saas-container {
        max-width: 680px;
        margin: 0 auto;
        padding: 3rem 1rem 6rem;
        position: relative;
    }

    /* Hero Card — idéntico a war-hero / dash-hero / pro-hero */
    .query-hero {
        background: rgba(255,255,255,0.025);
        border: 1px solid rgba(255,255,255,0.08);
        border-top: 2px solid rgba(168,85,247,0.5);
        border-radius: 1.5rem;
        padding: 1.5rem 1.25rem;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        transition: border-color 0.3s;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .query-hero:hover { border-top-color: rgba(168,85,247,0.8); }
    @media(max-width:640px) { .query-hero { padding: 1.25rem; flex-direction: column; align-items: stretch; } }

    .query-hero-title {
        font-size: 1.875rem;
        font-weight: 900;
        letter-spacing: -0.03em;
        background: linear-gradient(135deg, #fff 0%, #c4b5fd 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.25rem;
    }
    .query-hero-sub {
        color: rgba(148,163,184,0.65);
        font-size: 0.9rem;
    }

    /* Stat pill en el hero */
    .query-hero-stat {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(168,85,247,0.06);
        border: 1px solid rgba(168,85,247,0.2);
        padding: 0.35rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #c4b5fd;
        margin-top: 0.5rem;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        background-color: #34d399;
        border-radius: 50%;
        box-shadow: 0 0 10px #34d399;
        animation: pulse 2s infinite;
    }

    /* Main Form Card — alineado al sistema (glass-tbl de warranties) */
    .glass-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.5rem;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 1;
        transition: border-color 0.3s, transform 0.3s, box-shadow 0.3s;
    }
    .glass-card:hover {
        border-color: rgba(168,85,247,0.3);
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(168,85,247,0.1);
    }
    .glass-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #7c3aed, #a855f7, #ec4899, transparent);
        border-top-left-radius: 1.5rem;
        border-top-right-radius: 1.5rem;
    }
    
    .query-layout-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    /* PC Specific Enhancements */
    @media (min-width: 1024px) {
        .saas-container { max-width: 1000px; }
        .query-layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            align-items: start;
        }
    }

    /* Steps Layout (Timeline Style) */
    .step-group {
        position: relative;
        padding-left: 2.5rem;
        margin-bottom: 2.5rem;
    }
    .step-group:last-child {
        margin-bottom: 0;
    }
    
    /* Connecting Line */
    .step-group:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 2.5rem;
        bottom: -2.5rem;
        width: 2px;
        background: rgba(168,85,247,0.1);
    }

    .step-header {
        margin-bottom: 1.25rem;
    }
    .step-dot {
        position: absolute;
        left: 0;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        background: #111;
        border: 2px solid rgba(168,85,247,0.2);
        border-radius: 50%;
        color: #8b949e;
        font-size: 0.75rem;
        font-weight: 800;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 2;
    }
    .step-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #f8fafc;
        margin: 0;
        line-height: 1.5rem;
    }
    
    /* Step Active States */
    .step-group.active .step-dot {
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        border-color: transparent;
        color: #ffffff;
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.6);
        transform: scale(1.2);
    }
    .step-group.active:not(:last-child)::after {
        background: linear-gradient(to bottom, rgba(168, 85, 247, 0.8), rgba(168, 85, 247, 0.1));
    }

    /* Platform Grid */
    .platform-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 1rem;
    }
    .platform-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(168, 85, 247, 0.1);
        border-radius: 1.25rem;
        padding: 1.25rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.875rem;
    }
    .platform-item:hover {
        background: linear-gradient(145deg, rgba(168,85,247,0.08) 0%, rgba(168,85,247,0.02) 100%);
        border-color: rgba(168, 85, 247, 0.4);
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(168, 85, 247, 0.15);
    }
    .platform-item.selected {
        background: linear-gradient(145deg, rgba(168,85,247,0.15) 0%, rgba(236,72,153,0.05) 100%);
        border-color: rgba(168, 85, 247, 0.6);
        box-shadow: 0 8px 25px rgba(168, 85, 247, 0.25), inset 0 0 0 1px rgba(168,85,247,0.3);
        transform: translateY(-4px) scale(1.02);
    }
    .platform-img {
        width: 3rem;
        height: 3rem;
        border-radius: 0.875rem;
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        transition: transform 0.3s cubic-bezier(0.16,1,0.3,1);
    }
    .platform-item.selected .platform-img {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(168,85,247,0.4);
    }
    .platform-name {
        font-size: 0.8rem;
        font-weight: 600;
        color: #94a3b8;
        transition: color 0.3s ease;
    }
    .platform-item.selected .platform-name {
        color: #ffffff;
        font-weight: 800;
    }

    /* Custom Select with Search */
    .saas-select-wrapper {
        position: relative;
    }
    .saas-select {
        width: 100%;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(168,85,247,0.2);
        color: #f8fafc;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s ease;
        cursor: text;
    }
    .saas-select:focus {
        border-color: #a855f7;
        background: rgba(168,85,247,0.05);
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
    }
    .saas-select:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    
    /* Dropdown List */
    .saas-dropdown-list {
        margin-top: 0.5rem;
        background: rgba(15, 10, 24, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(168, 85, 247, 0.3);
        border-radius: 1rem;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        position: absolute;
        width: 100%;
        z-index: 100;
    }
    .saas-dropdown-list.show {
        display: block;
        animation: fade-in-up 0.2s ease;
    }
    .saas-dropdown-item {
        padding: 0.875rem 1.25rem;
        color: #e2e8f0;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        font-size: 0.9rem;
    }
    .saas-dropdown-item:last-child {
        border-bottom: none;
    }
    .saas-dropdown-item:hover {
        background: linear-gradient(90deg, rgba(168, 85, 247, 0.2), transparent);
        color: #ffffff;
    }

    .saas-select-icon {
        position: absolute;
        right: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: #a855f7;
        pointer-events: none;
        transition: transform 0.3s ease;
    }
    .saas-select-wrapper.open .saas-select-icon {
        transform: translateY(-50%) rotate(180deg);
    }

    /* Botón principal — idéntico a btn-war de warranties */
    .btn-submit-magic {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.625rem;
        background: linear-gradient(135deg, #7c3aed, #a855f7, #ec4899);
        border: none; border-radius: 0.875rem;
        color: white; font-weight: 800; font-size: 0.9rem; letter-spacing: 0.02em;
        padding: 0.875rem 1.75rem; text-decoration: none;
        box-shadow: 0 8px 25px rgba(168,85,247,0.4);
        transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
        position: relative; overflow: hidden;
        width: 100%; cursor: pointer;
    }
    .btn-submit-magic::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s ease;
    }
    .btn-submit-magic:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(168,85,247,0.55); }
    .btn-submit-magic:hover:not(:disabled)::after { transform: translateX(100%); }
    .btn-submit-magic:disabled {
        opacity: 0.4; cursor: not-allowed; transform: none !important;
    }

    /* Loaders */
    .loader-inline {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: #a855f7;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    .spinner-small {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid rgba(168, 85, 247, 0.2);
        border-top-color: #a855f7;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .fullscreen-loader {
        text-align: center;
        padding: 4rem 0;
    }
    .spinner-large {
        width: 4rem;
        height: 4rem;
        border: 3px solid rgba(168, 85, 247, 0.1);
        border-top-color: #ec4899;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.5rem;
    }

    /* Result View */
    .result-card {
        background: rgba(16, 185, 129, 0.05);
        border: 1px solid rgba(16, 185, 129, 0.15);
        border-radius: 1.75rem;
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }
    .result-icon {
        width: 5rem;
        height: 5rem;
        background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.05));
        border: 1px solid rgba(16,185,129,0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #34d399;
        box-shadow: 0 0 30px rgba(16,185,129,0.2);
    }
    @keyframes counter-pop {
        0%   { transform: scale(0.9); opacity: 0; }
        50%  { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes border-spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .result-code-wrap {
        position: relative;
        display: inline-block;
        margin: 1.5rem 0;
        animation: counter-pop 0.5s cubic-bezier(0.16,1,0.3,1);
    }
    .result-code-glow {
        display: none; /* Efecto 3D oculto por petición del usuario */
    }
    .result-code-box {
        position: relative; z-index: 1;
        background: #000000;
        border-radius: 1.25rem;
        padding: 1.5rem 2.5rem;
        font-family: 'Courier New', monospace;
        font-size: 3rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: inset 0 0 20px rgba(255,255,255,0.05);
    }
    .result-code-box:hover {
        transform: scale(1.02);
    }
    
    /* Error View */
    .error-card {
        text-align: center;
        padding: 3rem 0;
    }
    .error-icon {
        width: 5rem;
        height: 5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(239,68,68,0.05));
        border: 1px solid rgba(239,68,68,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #ef4444;
        box-shadow: 0 0 30px rgba(239,68,68,0.2);
    }

    /* Premium Stats Widget - God Level */
    .premium-stats-widget {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .premium-box {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.5rem;
        width: 100%;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 1.5rem;
        transition: border-color 0.3s, transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .premium-box:hover {
        border-color: rgba(168,85,247,0.3);
        box-shadow: 0 12px 40px rgba(168,85,247,0.1);
        transform: translateY(-2px);
    }
    
    /* Top Box: Selected Platform */
    .selected-platform-display {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.875rem;
    }
    .selected-platform-icon {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 1.25rem;
        object-fit: cover;
        box-shadow: 0 8px 25px rgba(168,85,247,0.2);
    }
    .selected-platform-icon-fallback {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, rgba(168,85,247,0.2), rgba(236,72,153,0.1));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border: 1px solid rgba(168,85,247,0.2);
    }
    .selected-platform-name {
        color: #ffffff;
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: 0.02em;
    }
    
    /* Bottom Box: Stats rows */
    .premium-stat-row {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(168,85,247,0.1);
        border-radius: 1rem;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 0.75rem;
        transition: background 0.3s ease, border-color 0.3s;
    }
    .premium-stat-row:hover {
        background: rgba(168,85,247,0.05);
        border-color: rgba(168,85,247,0.3);
    }
    .premium-stat-row:last-child {
        margin-bottom: 0;
    }
    .premium-stat-icon {
        width: 2.75rem; height: 2.75rem;
        background: rgba(168,85,247,0.1);
        border-radius: 0.75rem;
        color: #a855f7;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .premium-stat-icon svg {
        width: 1.5rem;
        height: 1.5rem;
    }
    .premium-stat-text {
        display: flex;
        flex-direction: column;
        text-align: left;
    }
    .premium-stat-label {
        color: rgba(148,163,184,0.8);
        font-size: 0.72rem;
        font-weight: 600;
        margin-bottom: 0.15rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .premium-stat-value {
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .platform-icon-fallback {
        width: 3rem;
        height: 3rem;
        background: rgba(168,85,247,0.1);
        border: 1px solid rgba(168,85,247,0.2);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* ===== MOBILE RESPONSIVENESS ===== */
    @media (max-width: 640px) {
        .saas-container {
            padding: 2rem 1rem 4rem;
        }
        .hero-title {
            font-size: 1.75rem;
        }
        .glass-card {
            padding: 1.5rem;
        }
        .step-group {
            padding-left: 2rem;
        }
        .step-group:not(:last-child)::after {
            left: 0.5rem;
        }
        .step-dot {
            width: 1.25rem;
            height: 1.25rem;
            font-size: 0.65rem;
        }
        .platform-grid {
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        }
        .result-code-box {
            font-size: 2.25rem;
            padding: 1rem 1.25rem;
            letter-spacing: 0.1em;
            word-break: break-all;
        }
    }
</style>
@endsection

@section('content')
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="saas-container">

    {{-- ===== RESULT: EMAIL FOUND EN SESIÓN ===== --}}
    @if(session('reseller_email_body') && session('reseller_temp_code_expiry') && now()->timestamp < session('reseller_temp_code_expiry'))
        
        <div class="query-hero">
            <div>
                <h1 class="query-hero-title">Verificación Exitosa</h1>
                <p class="query-hero-sub">Tu código o enlace de acceso está listo.</p>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span class="status-dot"></span>
                <span style="font-size:0.78rem;font-weight:700;color:#34d399;text-transform:uppercase;letter-spacing:0.1em;">Activo</span>
            </div>
        </div>

        <div class="result-card">
            <div class="result-icon">
                <svg style="width:2rem;height:2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 style="font-size:1.5rem;font-weight:800;color:#ffffff;margin-bottom:0.5rem;" id="codeTitleLabel">Tu código de Verificación</h3>
            <p style="color:#94a3b8;font-size:0.95rem;margin-bottom:2rem;">Llegó hace un momento. Tienes 15 minutos antes de que el servidor lo borre por seguridad.</p>

            @php
                $extracted = session('reseller_extracted_code');
                $codeType = $extracted['type'] ?? 'html';
                $codeValue = $extracted['value'] ?? session('reseller_email_body');
            @endphp

            @if($codeType === 'code')
                <div class="result-code-wrap">
                    <div class="result-code-glow"></div>
                    <div class="result-code-box" onclick="copyText('{{ $codeValue }}', this)" title="Copiar código">
                        {{ $codeValue }}
                    </div>
                </div>
            @elseif($codeType === 'link')
                <div style="text-align: center; margin: 2rem 0;">
                    <a href="{{ $codeValue }}" target="_blank" style="display:inline-flex;align-items:center;gap:0.5rem;background:#ffffff;color:#000000;font-weight:700;padding:1rem 2rem;border-radius:0.75rem;text-decoration:none;transition:all 0.2s;">
                        Abrir Enlace Mágico
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            @else
                <div class="code-display" onclick="copyText('{{ route('admin.query.code') }}', this)">
                    <iframe src="{{ route('admin.query.code') }}" style="width:100%; height:400px; border:none; border-radius:1rem; background:#fff;"></iframe>
                </div>
            @endif

            <div class="action-buttons" style="text-align:center; margin-top:2rem;">
                <a href="{{ route('admin.query.clear') }}" class="btn-secondary-magic" style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); padding:0.75rem 1.5rem; border-radius:0.5rem; color:#fff; text-decoration:none; font-weight:600; transition:all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                    <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Consultar Otro Correo
                </a>
                <div style="font-size:0.85rem;color:#64748b;margin-top:1rem;">
                    Expira en <span id="countdown" data-remaining="{{ session('reseller_temp_code_expiry') - now()->timestamp }}" style="color:#f8fafc;font-weight:700;"></span>
                </div>
            </div>
        </div>

    @else
        {{-- ===== MAIN QUERY FLOW ===== --}}
        
        <div class="query-hero">
            <div>
                <h1 class="query-hero-title">Consultar Código</h1>
                <p class="query-hero-sub">Selecciona la plataforma y el correo, nosotros hacemos el resto.</p>

            </div>
            <div style="text-align:right;">
                <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(168,85,247,0.8);margin-bottom:0.25rem;">Estado</div>
                <div style="font-size:1.25rem;font-weight:900;color:#34d399;line-height:1;">Ilimitadas</div>
                <div style="font-size:0.72rem;color:rgba(148,163,184,0.5);margin-top:0.15rem;">Modo Administrador</div>
            </div>
        </div>

        <div class="query-layout-grid">
            <div class="glass-card" id="queryFlowCard">


            <form id="queryForm" onsubmit="event.preventDefault(); handleSearch();">
                @csrf
                <input type="hidden" name="platform_id" id="selectedPlatformId">

                {{-- STEP 1: Platform --}}
                <div class="step-group active" id="step1">
                    <div class="step-header">
                        <div class="step-dot">1</div>
                        <h3 class="step-title">Selecciona la Plataforma</h3>
                    </div>
                    
                    <div class="platform-grid">
                        @foreach($platforms as $platform)
                            <div class="platform-item" id="plat-btn-{{ $platform->id }}" onclick="selectPlatform({{ $platform->id }}, {{ json_encode($platform->name) }}, {{ json_encode($platform->logo ? asset(str_starts_with($platform->logo, 'platforms_logos') ? $platform->logo : 'storage/' . $platform->logo) : '') }})">
                                @if($platform->logo)
                                    <img src="{{ asset(str_starts_with($platform->logo, 'platforms_logos') ? $platform->logo : 'storage/' . $platform->logo) }}" alt="{{ $platform->name }}" class="platform-img" style="width:3rem; height:3rem; object-fit:contain; border-radius:0.5rem;">
                                @else
                                    <div class="platform-icon-fallback">📺</div>
                                @endif
                                <span class="platform-name">{{ $platform->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- STEP 2: Email (Initially Hidden) --}}
                <div class="step-group" id="step2" style="display: none; opacity: 0; transition: opacity 0.4s ease;">
                    <div class="step-header">
                        <div class="step-dot" id="step2-dot">2</div>
                        <h3 class="step-title">Elige el Correo Asignado</h3>
                    </div>
                    
                    <div id="email-loader" class="loader-inline" style="display:none;">
                        <div class="spinner-small"></div>
                        Cargando bandeja...
                    </div>

                    <div class="saas-select-wrapper" id="emailDropdownWrapper">
                        <input type="email" id="emailSelect" class="saas-select" placeholder="Haz clic, escribe o pega el correo..." oninput="filterDropdown(); checkFormReady()" onfocus="openDropdown()" autocomplete="off" disabled>
                        
                        <!-- Custom Dropdown Menu -->
                        <div id="customEmailList" class="saas-dropdown-list">
                            <!-- Opciones inyectadas via JS -->
                        </div>

                        <svg class="saas-select-icon" style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                {{-- Speedy Gonzalez State (Initially Hidden) --}}
                <div class="step-group" id="speedyGonzalezState" style="display: none; opacity: 0; margin-top: 1.5rem; transition: opacity 0.4s ease;">
                    <div class="step-header">
                        <div class="step-dot active" style="background:#ef4444;border-color:#fca5a5;color:#fff;box-shadow:0 0 20px rgba(239,68,68,0.6);transform:scale(1.1);">!</div>
                        <h3 class="step-title" style="color:#fca5a5;">Tiempo de Espera Activo</h3>
                    </div>
                    <div style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.2);padding:1.5rem;border-radius:1rem;text-align:center;">
                        <h4 style="font-size:1.1rem;font-weight:700;color:#ffffff;margin-bottom:0.5rem;">Espera un momento Speedy Gonzalez, vas muy a prisa</h4>
                        <p style="color:#94a3b8;font-size:0.9rem;margin-bottom:1rem;">Podrás volver a consultar este correo en:</p>
                        <div id="speedyCountdown" style="font-size:2rem;font-weight:900;color:#ef4444;font-family:monospace;letter-spacing:0.1em;margin-bottom:1.5rem;">00:00</div>
                        
                        <button type="button" onclick="clearSelectedEmail()" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#e2e8f0;padding:0.75rem 2rem;border-radius:0.75rem;font-size:0.9rem;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                            Cerrar / Elegir otro correo
                        </button>
                    </div>
                </div>

                {{-- STEP 3: Submit (Initially Hidden) --}}
                <div class="step-group" id="step3" style="display: none; opacity: 0; margin-top: 1.5rem; transition: opacity 0.4s ease;">
                    <div class="step-header">
                        <div class="step-dot active" style="background:#a855f7;border-color:#d8b4fe;color:#fff;box-shadow:0 0 20px rgba(168,85,247,0.6);transform:scale(1.1);">3</div>
                        <h3 class="step-title">Obtener Código</h3>
                    </div>
                    <button type="submit" id="queryBtn" class="btn-submit-magic">
                        <svg id="btnIcon" style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span id="btnText">Buscar Código Mágico</span>
                    </button>
                </div>
            </form>

            {{-- Loading State --}}
            <div id="loadingState" class="fullscreen-loader" style="display:none;position:relative;z-index:2;">
                <div class="spinner-large"></div>
                <h3 style="font-size:1.25rem;font-weight:700;color:#ffffff;margin-bottom:0.5rem;" id="loadingTitle">Conectando a los servidores...</h3>
                <p style="color:#94a3b8;font-size:0.95rem;" id="loadingDesc">Buscando el código en tiempo real.</p>
            </div>

            {{-- Error State --}}
            <div id="errorState" class="error-card" style="display:none;position:relative;z-index:2;">
                <div class="error-icon">
                    <svg style="width:2rem;height:2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 style="font-size:1.25rem;font-weight:700;color:#ffffff;margin-bottom:0.5rem;">No se encontró el código</h3>
                <p id="errorMessage" style="color:#94a3b8;font-size:0.95rem;margin-bottom:2rem;">El correo de verificación aún no ha llegado a la bandeja.</p>
                <button type="button" onclick="resetForm()" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#e2e8f0;padding:0.75rem 2rem;border-radius:0.75rem;font-size:0.9rem;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                    Intentar de Nuevo
                </button>
            </div>
            </div>
        
        {{-- Premium Stats Widget --}}
        @if($setting ?? null)
            @php 
                $siteName = \App\Models\Setting::get('site_name', 'Sistema de Códigos');
            @endphp
            
            <div class="premium-stats-widget">
            <!-- Selected Platform Card -->
            <div class="premium-box" id="selectedPlatformCard" style="display: none;">
                <div style="text-align:center; color:#8b949e; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; font-weight:600; margin-bottom:0.5rem;">Plataforma Seleccionada</div>
                <div class="selected-platform-display">
                    <img src="" id="displayPlatformIcon" class="selected-platform-icon" style="display: none;">
                    <div id="displayPlatformName" class="selected-platform-name" style="color:#ffffff;">...</div>
                </div>
            </div>
            
            <!-- IMAP Status Badge Removed -->

            <!-- Stats Card -->
            <div class="premium-box">
                <div class="premium-stat-row">
                    <div class="premium-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="premium-stat-text">
                        <div class="premium-stat-label">Tiempo de Espera</div>
                        <div class="premium-stat-value" id="cooldownText">Sin límites de espera</div>
                    </div>
                </div>
                
                <div class="premium-stat-row">
                    <div class="premium-stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div class="premium-stat-text">
                        <div class="premium-stat-label">Consultas Hoy</div>
                        <div class="premium-stat-value" id="queryCountText">Ilimitadas</div>
                    </div>
                </div>
            </div>
        </div>
        </div> <!-- End query-layout-grid -->
    @endif <!-- End if setting -->
    
    @endif <!-- End if session(email_body) -->
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const savedName = localStorage.getItem('queried_platform_name');
        const label = document.getElementById('codeTitleLabel');
        if (savedName && label) {
            label.textContent = 'Tu código de ' + savedName + ' (Clic para copiar)';
        }
    });

    // ===== STATE =====
    let _platformId = null;
    let _platformName = '';
    let _retryCount = 0;
    let _availableEmailsData = [];
    let _availableEmails = [];
    let _cooldownSeconds = 0;
    let _cooldownInterval = null;
    let _speedyInterval = null;
    const MAX_RETRIES = 9; // 1.5 minutos (9 intentos x 10s)
    const RETRY_DELAY_MS = 10000;

    // Cierra el dropdown al hacer clic afuera
    document.addEventListener('click', function(event) {
        const wrapper = document.getElementById('emailDropdownWrapper');
        if (wrapper && !wrapper.contains(event.target)) {
            closeDropdown();
        }
    });

    window.openDropdown = function() {
        if (!document.getElementById('emailSelect').disabled && _availableEmails.length > 0) {
            document.getElementById('customEmailList').classList.add('show');
            document.getElementById('emailDropdownWrapper').classList.add('open');
            renderDropdownList(_availableEmails); // Mostrar todos al abrir
        }
    }

    window.closeDropdown = function() {
        const listDiv = document.getElementById('customEmailList');
        const wrapper = document.getElementById('emailDropdownWrapper');
        if (listDiv) listDiv.classList.remove('show');
        if (wrapper) wrapper.classList.remove('open');
    }

    window.filterDropdown = function() {
        const query = document.getElementById('emailSelect').value.toLowerCase();
        const filtered = _availableEmails.filter(e => e.toLowerCase().includes(query));
        renderDropdownList(filtered);
        if (filtered.length > 0) {
            document.getElementById('customEmailList').classList.add('show');
            document.getElementById('emailDropdownWrapper').classList.add('open');
        }
    }

    window.selectEmailOption = function(emailStr) {
        const input = document.getElementById('emailSelect');
        input.value = emailStr;
        closeDropdown();
        checkFormReady();
    }

    function renderDropdownList(emailsArray) {
        const listDiv = document.getElementById('customEmailList');
        if (!listDiv) return;
        listDiv.innerHTML = '';
        if(emailsArray.length === 0) {
            listDiv.innerHTML = '<div style="padding:0.75rem 1.25rem;color:#94a3b8;font-size:0.85rem;">No hay coincidencias</div>';
            return;
        }
        emailsArray.forEach(email => {
            const div = document.createElement('div');
            div.className = 'saas-dropdown-item';
            div.textContent = email;
            div.onclick = function(e) {
                e.stopPropagation();
                selectEmailOption(email);
            };
            listDiv.appendChild(div);
        });
    }

    // ===== PASO 1 =====
    window.selectPlatform = function(id, name, logoUrl = '') {
        _platformId = id;
        _platformName = name;
        document.getElementById('selectedPlatformId').value = id;

        // Update Premium Widget
        const displayCard = document.getElementById('selectedPlatformCard');
        document.getElementById('displayPlatformName').innerText = name;
        document.getElementById('displayPlatformName').style.color = '#ffffff';
        if(logoUrl) {
            document.getElementById('displayPlatformIcon').src = logoUrl;
            document.getElementById('displayPlatformIcon').style.display = 'block';
        } else {
            document.getElementById('displayPlatformIcon').style.display = 'none';
        }
        displayCard.style.display = 'flex';

        // UI Updates
        document.querySelectorAll('.platform-item').forEach(btn => btn.classList.remove('selected'));
        document.getElementById('plat-btn-' + id).classList.add('selected');

        const step2 = document.getElementById('step2');
        step2.style.display = 'block'; // Mostrar la caja
        setTimeout(() => {
            step2.style.opacity = '1';
            step2.classList.add('active');
        }, 10);
        
        const select = document.getElementById('emailSelect');
        const listDiv = document.getElementById('customEmailList');
        
        select.value = '';
        _availableEmails = [];
        if (listDiv) listDiv.innerHTML = '';
        
        select.placeholder = 'Cargando...';
        document.getElementById('email-loader').style.display = 'flex';
        select.disabled = true;
        closeDropdown();
        
        resetStep3();

        setTimeout(() => {
            document.getElementById('email-loader').style.display = 'none';
            const allEmails = [
                @foreach($allowedEmails as $ae)
                    { email: '{{ $ae->email }}', cooldown: 0 },
                @endforeach
            ];
            
            if (allEmails.length > 0) {
                select.disabled = false;
                select.placeholder = 'Haz clic para ver o escribir...';
                
                _availableEmailsData = allEmails;
                _availableEmails = allEmails.map(e => e.email);
                renderDropdownList(_availableEmails);

                if (allEmails.length === 1) {
                    select.value = allEmails[0].email;
                    checkFormReady();
                }
            } else {
                select.placeholder = 'Sin correos en inventario';
                select.disabled = true;
            }
        }, 100);
    }

    // ===== PASO 2 =====
    window.checkFormReady = function() {
        const email = document.getElementById('emailSelect').value.trim();
        const step3 = document.getElementById('step3');

        // Solo mostrar Paso 3 si el correo coincide exactamente con uno de los asignados
        const isValidMatch = _availableEmails.some(e => e.toLowerCase() === email.toLowerCase());

        if (isValidMatch) {
            const emailData = _availableEmailsData.find(e => e.email.toLowerCase() === email.toLowerCase());
            
            if (emailData && emailData.cooldown > 0) {
                resetStep3();
                showSpeedyGonzalez(emailData.cooldown);
                return;
            }
        }
        
        hideSpeedyGonzalez();

        // Mostrar solo si hay match
        if (_platformId && isValidMatch) {
            step3.style.display = 'block';
            setTimeout(() => { step3.style.opacity = '1'; }, 10);
        } else {
            resetStep3();
        }
    }

    window.clearSelectedEmail = function() {
        const select = document.getElementById('emailSelect');
        select.value = '';
        hideSpeedyGonzalez();
        checkFormReady();
        select.focus();
    }

    function showSpeedyGonzalez(seconds) {
        const container = document.getElementById('speedyGonzalezState');
        const countdownEl = document.getElementById('speedyCountdown');
        
        if (container) {
            container.style.display = 'block';
            setTimeout(() => { container.style.opacity = '1'; }, 10);
        }
        
        if (_speedyInterval) clearInterval(_speedyInterval);
        
        let remaining = seconds;
        const update = () => {
            if (remaining <= 0) {
                clearInterval(_speedyInterval);
                hideSpeedyGonzalez();
                
                // Actualizar el data localmente para permitir consulta
                const email = document.getElementById('emailSelect').value.trim();
                const emailData = _availableEmailsData.find(e => e.email.toLowerCase() === email.toLowerCase());
                if (emailData) emailData.cooldown = 0;
                
                checkFormReady(); // Vuelve a chequear, ahora debería permitirlo
                return;
            }
            const mm = Math.floor(remaining / 60);
            const ss = remaining % 60;
            if (countdownEl) countdownEl.textContent = mm + ':' + String(ss).padStart(2, '0');
            remaining--;
        };
        
        update();
        _speedyInterval = setInterval(update, 1000);
    }
    
    function hideSpeedyGonzalez() {
        const container = document.getElementById('speedyGonzalezState');
        if (container) {
            container.style.opacity = '0';
            setTimeout(() => { container.style.display = 'none'; }, 400);
        }
        if (_speedyInterval) clearInterval(_speedyInterval);
    }

    function resetStep3() {
        const step3 = document.getElementById('step3');
        step3.style.opacity = '0';
        setTimeout(() => { step3.style.display = 'none'; }, 400); // esperar transición
    }

    // ===== PASO 3 =====
    window.handleSearch = function() {
        const email = document.getElementById('emailSelect').value;
        if (!_platformId || !email) return;

        localStorage.setItem('queried_platform_name', _platformName);

        document.getElementById('queryForm').style.display = 'none';
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('errorState').style.display = 'none';
        
        _retryCount = 0;
        executeBackendQuery(email);
    }

    function executeBackendQuery(email) {
        document.getElementById('loadingTitle').textContent = _retryCount > 0 ? `Reintentando (${_retryCount}/${MAX_RETRIES})...` : 'Conectando a los servidores...';
        document.getElementById('loadingDesc').textContent = _retryCount > 0 ? 'Esperando que llegue a la bandeja...' : 'Buscando el código en tiempo real.';

        const formData = new FormData();
        formData.append('platform_id', _platformId);
        formData.append('email', email);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Indicar si es el último intento para que el backend aplique el cooldown
        if (_retryCount >= MAX_RETRIES - 1) {
            formData.append('is_final_attempt', '1');
        }

        fetch(`{{ route('admin.query.post') }}`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                if (data.result === 'no_code' && _retryCount < MAX_RETRIES) {
                    _retryCount++;
                    setTimeout(() => executeBackendQuery(email), RETRY_DELAY_MS);
                } else {
                    showError(data.message || 'No se encontró el código.');
                }
            }
        })
        .catch(() => {
            showError('Error de conexión.');
        });
    }

    function showError(message) {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('errorState').style.display = 'block';
        document.getElementById('errorMessage').textContent = message;
    }

    window.resetForm = function() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('errorState').style.display = 'none';
        document.getElementById('queryForm').style.display = 'block';
    }

    window.copyText = function(text, element) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = element.textContent;
            element.textContent = '¡Copiado!';
            element.style.color = '#34d399';
            setTimeout(() => {
                element.textContent = originalText;
                element.style.color = '#ffffff';
            }, 2000);
        });
    }

    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        let remaining = parseInt(countdownEl.getAttribute('data-remaining'));
        const updateDisplay = () => {
            const mm = Math.floor(remaining / 60);
            const ss = remaining % 60;
            countdownEl.textContent = mm + ':' + String(ss).padStart(2, '0');
            if (remaining <= 10) countdownEl.style.color = '#ef4444';
        };
        updateDisplay(); // mostrar inmediatamente
        setInterval(() => {
            if (remaining > 0) {
                remaining--;
                updateDisplay();
            } else {
                window.location.reload();
            }
        }, 1000);
    }
    // Verificación IMAP eliminada

    // ===== LÍMITES DINÁMICOS =====
    function startCooldownTimer() {
        if (_cooldownInterval) clearInterval(_cooldownInterval);
        
        _cooldownInterval = setInterval(() => {
            if (_cooldownSeconds > 0) {
                _cooldownSeconds--;
                const cooldownText = document.getElementById('cooldownText');
                
                const minutes = Math.floor(_cooldownSeconds / 60);
                const seconds = _cooldownSeconds % 60;
                cooldownText.textContent = `${minutes}:${seconds.toString().padStart(2, '0')} MIN`;
                cooldownText.style.color = '#f87171';
                
                // Forzar ocultar el botón si está en cooldown
                checkFormReady();
            } else {
                clearInterval(_cooldownInterval);
                const cooldownText = document.getElementById('cooldownText');
                cooldownText.textContent = 'Listo para consultar';
                cooldownText.style.color = '#34d399';
                
                // Mostrar botón si cumple el resto de condiciones
                checkFormReady();
            }
        }, 1000);
    }

    function updateLimits() {
        // Nada que actualizar para administradores
    }

    // Inicializar estado del sistema al cargar
    document.addEventListener('DOMContentLoaded', function() {
        // Nada
    });

</script>
@endpush
