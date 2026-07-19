<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('site_name', 'Tu Código') }} — Acceso al Sistema</title>
    <meta name="description" content="Panel de gestión empresarial — Acceso seguro al sistema">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #050510;
            display: flex;
            flex-direction: column;
            color: white;
            overflow-x: hidden;
        }

        /* ═══ GRID BACKGROUND ═══ */
        .bg-scene {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image:
                linear-gradient(rgba(168,85,247,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168,85,247,0.03) 1px, transparent 1px);
            background-size: 56px 56px;
        }
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            animation: drift 10s ease-in-out infinite alternate;
        }
        .orb-1 { width:600px;height:600px;background:radial-gradient(circle,rgba(124,58,237,0.16) 0%,transparent 70%);top:-150px;left:-100px;animation-delay:0s; }
        .orb-2 { width:500px;height:500px;background:radial-gradient(circle,rgba(236,72,153,0.10) 0%,transparent 70%);bottom:-120px;right:-80px;animation-delay:-4s; }
        .orb-3 { width:350px;height:350px;background:radial-gradient(circle,rgba(99,102,241,0.08) 0%,transparent 70%);top:40%;left:55%;animation-delay:-7s; }
        @keyframes drift { from{transform:translate(0,0) scale(1);} to{transform:translate(25px,-18px) scale(1.04);} }

        /* ═══ LAYOUT ═══ */
        .page-wrapper {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .login-container {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            border-radius: 1.75rem;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(168,85,247,0.1), 0 0 80px rgba(168,85,247,0.06);
        }
        @media (min-width: 768px) {
            .login-container { grid-template-columns: 1fr 1fr; }
        }

        /* ═══ PANEL IZQUIERDO — BRANDING ═══ */
        .brand-panel {
            background: linear-gradient(145deg, rgba(14,8,35,0.98) 0%, rgba(8,4,20,1) 100%);
            border-right: 1px solid rgba(168,85,247,0.1);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .brand-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899);
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(168,85,247,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            margin-bottom: 2.5rem;
        }
        .brand-icon {
            width: 3rem; height: 3rem;
            background: linear-gradient(135deg, rgba(124,58,237,0.22), rgba(236,72,153,0.12));
            border: 1px solid rgba(168,85,247,0.35);
            border-radius: 0.875rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 20px rgba(168,85,247,0.2);
            flex-shrink: 0;
        }
        .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, #f1f5f9, #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-tagline {
            font-size: 0.65rem;
            font-weight: 600;
            color: rgba(100,116,139,0.6);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 0.1rem;
        }
        .brand-headline {
            font-size: 1.875rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        .brand-headline .highlight {
            background: linear-gradient(135deg, #a855f7, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-desc {
            font-size: 0.88rem;
            color: rgba(148,163,184,0.65);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .feature-list { display: flex; flex-direction: column; gap: 0.75rem; }
        .feature-item {
            display: flex; align-items: center; gap: 0.75rem;
        }
        .feature-dot {
            width: 1.75rem; height: 1.75rem; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .feature-text { font-size: 0.82rem; color: rgba(203,213,225,0.75); font-weight: 500; }

        .status-bar {
            display: flex; align-items: center; gap: 0.5rem;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(168,85,247,0.08);
        }
        .status-dot { width:0.5rem;height:0.5rem;border-radius:50%;background:#34d399;box-shadow:0 0 6px rgba(52,211,153,0.7);flex-shrink:0; }
        .status-text { font-size: 0.7rem; color: rgba(100,116,139,0.6); font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; }
        .status-badge {
            margin-left: auto;
            font-size: 0.6rem; font-weight: 700;
            background: linear-gradient(135deg, rgba(124,58,237,0.2), rgba(236,72,153,0.1));
            border: 1px solid rgba(168,85,247,0.25);
            border-radius: 9999px;
            padding: 0.2rem 0.6rem;
            color: #c4b5fd;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* ═══ PANEL DERECHO — FORMULARIO ═══ */
        .form-panel {
            background: linear-gradient(145deg, rgba(12,7,30,0.99) 0%, rgba(8,4,18,1) 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        .form-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(168,85,247,0.4), rgba(236,72,153,0.3), transparent);
        }
        @media(min-width:768px) {
            .form-panel::before { left: 0; top: 0; bottom: 0; right: auto; width: 1px; height: auto;
                background: linear-gradient(180deg, transparent, rgba(168,85,247,0.15), transparent); }
        }

        .form-header { margin-bottom: 2rem; }
        .form-title { font-size: 1.5rem; font-weight: 800; color: #f1f5f9; margin-bottom: 0.35rem; }
        .form-sub { font-size: 0.8rem; color: rgba(100,116,139,0.65); }

        /* Error */
        .error-box {
            background: rgba(239,68,68,0.07); border: 1px solid rgba(239,68,68,0.22);
            border-radius: 0.75rem; padding: 0.8rem 1rem;
            color: rgba(252,165,165,1); font-size: 0.82rem;
            display: flex; align-items: flex-start; gap: 0.5rem; margin-bottom: 1.25rem;
        }

        /* Fields */
        .field { margin-bottom: 1.125rem; }
        .field-label {
            display: block; font-size: 0.72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.07em;
            color: rgba(168,85,247,0.85); margin-bottom: 0.45rem;
        }
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%);
            pointer-events: none; color: rgba(168,85,247,0.45);
        }
        .field-input {
            width: 100%; background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(168,85,247,0.13);
            border-radius: 0.75rem;
            padding: 0.8rem 1rem 0.8rem 2.6rem;
            color: white; font-size: 0.88rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease; outline: none;
        }
        .field-input::placeholder { color: rgba(100,116,139,0.4); }
        .field-input:focus {
            border-color: rgba(168,85,247,0.45);
            background: rgba(168,85,247,0.05);
            box-shadow: 0 0 0 3px rgba(168,85,247,0.09);
        }

        /* Remember */
        .remember-row { display:flex;align-items:center;gap:0.5rem;margin-bottom:1.5rem; }
        .remember-row input[type="checkbox"] { width:0.9rem;height:0.9rem;accent-color:#a855f7;cursor:pointer; }
        .remember-row label { font-size:0.8rem;color:rgba(148,163,184,0.7);font-weight:400;cursor:pointer;user-select:none; }

        /* Button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
            border: none; border-radius: 0.875rem;
            color: white; font-size: 0.9rem; font-weight: 700;
            font-family: 'Inter', sans-serif;
            padding: 0.9rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            box-shadow: 0 4px 20px rgba(168,85,247,0.38), 0 0 0 1px rgba(168,85,247,0.15);
            transition: all 0.25s ease;
            position: relative; overflow: hidden;
            letter-spacing: 0.02em;
        }
        .btn-login::after { content:'';position:absolute;inset:0;background:linear-gradient(135deg,transparent,rgba(255,255,255,0.07));opacity:0;transition:opacity 0.2s; }
        .btn-login:hover { box-shadow:0 8px 32px rgba(168,85,247,0.52),0 0 0 1px rgba(168,85,247,0.25);transform:translateY(-1px); }
        .btn-login:hover::after { opacity:1; }
        .btn-login:active { transform:translateY(0); }

        /* Trust badges */
        .trust-row { display:flex;justify-content:center;gap:0.625rem;margin-top:1.5rem;flex-wrap:wrap; }
        .trust-badge {
            display:inline-flex;align-items:center;gap:0.3rem;
            font-size:0.67rem;font-weight:600;color:rgba(100,116,139,0.55);
            background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);
            border-radius:9999px;padding:0.25rem 0.65rem;
        }

        /* ═══ FOOTER ═══ */
        .page-footer {
            position: relative; z-index: 1;
            padding: 1.25rem 2rem;
            border-top: 1px solid rgba(168,85,247,0.07);
            background: rgba(5,5,16,0.6);
            backdrop-filter: blur(10px);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 0.75rem;
        }
        .footer-left { font-size: 0.72rem; color: rgba(100,116,139,0.5); }
        .footer-left strong {
            background: linear-gradient(135deg, #a855f7, #ec4899);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            font-weight: 700;
        }
        .footer-center {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.68rem; color: rgba(100,116,139,0.4);
        }
        .footer-sep { opacity: 0.3; }
        .footer-right { display:flex;align-items:center;gap:0.375rem; }
        .footer-status { width:0.4rem;height:0.4rem;border-radius:50%;background:#34d399;box-shadow:0 0 5px rgba(52,211,153,0.6); }
        .footer-ver { font-size:0.67rem;color:rgba(100,116,139,0.4);font-weight:600;text-transform:uppercase;letter-spacing:0.06em; }

        /* Mobile brand panel hidden */
        @media (max-width: 767px) {
            .brand-panel { display: none; }
            .login-container { max-width: 420px; }
            .form-panel { padding: 2.5rem 2rem; border-radius: 1.75rem; }
        }
    </style>
</head>
<body>
    @php
        $siteName = \App\Models\Setting::get('site_name', 'Tu Código');
        $siteLogo = \App\Models\Setting::get('site_logo', null);
    @endphp

    <!-- Background -->
    <div class="bg-scene"></div>
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    <div class="page-wrapper">
        <div class="main-content">
            <div class="login-container">

                <!-- ══ PANEL IZQUIERDO — BRANDING ══ -->
                <div class="brand-panel">
                    <!-- Logo -->
                    <div>
                        <div class="brand-logo">
                            <div class="brand-icon">
                                @if($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($siteLogo) }}" alt="{{ $siteName }}" style="width:2rem;height:2rem;object-fit:contain;">
                                @else
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="url(#lg1)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <defs><linearGradient id="lg1" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#818cf8"/><stop offset="100%" stop-color="#c084fc"/></linearGradient></defs>
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        <circle cx="12" cy="12" r="2" fill="#a855f7" stroke="none"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="brand-name">{{ $siteName }}</div>
                                <div class="brand-tagline">Sistema de Gestión Empresarial</div>
                            </div>
                        </div>

                        <h2 class="brand-headline">
                            Gestión inteligente<br>para tu <span class="highlight">franquicia</span>
                        </h2>
                        <p class="brand-desc">
                            Controla plataformas, servidores, licencias y clientes desde un único panel diseñado para operar a escala empresarial.
                        </p>

                        <div class="feature-list">
                            <div class="feature-item">
                                <div class="feature-dot" style="background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.3);">
                                    <svg width="13" height="13" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="feature-text">Gestión de licencias y franquicias</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-dot" style="background:rgba(236,72,153,0.12);border:1px solid rgba(236,72,153,0.25);">
                                    <svg width="13" height="13" fill="none" stroke="#ec4899" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                                </div>
                                <span class="feature-text">Control de servidores y plataformas</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-dot" style="background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.22);">
                                    <svg width="13" height="13" fill="none" stroke="#34d399" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </div>
                                <span class="feature-text">Reportes y métricas en tiempo real</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="status-bar">
                        <span class="status-dot"></span>
                        <span class="status-text">Todos los sistemas operativos</span>
                        <span class="status-badge">v3.0 Enterprise</span>
                    </div>
                </div>

                <!-- ══ PANEL DERECHO — FORMULARIO ══ -->
                <div class="form-panel">
                    <div class="form-header">
                        <h1 class="form-title">Bienvenido de vuelta</h1>
                        <p class="form-sub">Inicia sesión para acceder al panel de control</p>
                    </div>

                    @if($errors->any())
                        <div class="error-box">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        <!-- Email -->
                        <div class="field">
                            <label class="field-label" for="email">
                                Correo Electrónico
                            </label>
                            <div class="field-wrap">
                                <span class="field-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    required autofocus autocomplete="email"
                                    class="field-input" placeholder="tu@empresa.com">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="field">
                            <label class="field-label" for="password">
                                Contraseña
                            </label>
                            <div class="field-wrap">
                                <span class="field-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </span>
                                <input type="password" id="password" name="password"
                                    required autocomplete="current-password"
                                    class="field-input" placeholder="••••••••••">
                            </div>
                        </div>

                        <!-- Remember me -->
                        <div class="remember-row">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Mantener sesión iniciada</label>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn-login">
                            <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Ingresar al Sistema
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>

                    <!-- Trust badges -->
                    <div class="trust-row">
                        <span class="trust-badge">
                            <svg width="9" height="9" fill="none" stroke="#34d399" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            SSL Seguro
                        </span>
                        <span class="trust-badge">
                            <svg width="9" height="9" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Cifrado AES-256
                        </span>
                        <span class="trust-badge">
                            <svg width="9" height="9" fill="none" stroke="#ec4899" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Alta Disponibilidad
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <x-footer type="login" />
    </div>

    <!-- Auto-refresh para evitar error 419 (CSRF token expiration) -->
    <script>
        // Recargar la página automáticamente unos minutos antes de que expire la sesión
        setTimeout(function() {
            window.location.reload();
        }, ({{ config('session.lifetime', 120) }} - 2) * 60 * 1000);
    </script>
</body>
</html>
