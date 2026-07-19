<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Consultar Código' }} — {{ \App\Models\Setting::get('site_name', 'Tu Código') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --violet: #7c3aed;
            --violet-mid: #a855f7;
            --magenta: #ec4899;
            --bg: #050510;
            --bg2: #0a0618;
            --border: rgba(168,85,247,0.18);
            --text: rgba(226,232,240,1);
            --muted: rgba(148,163,184,0.65);
        }

        html, body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ===== AMBIENT BG ===== */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(168,85,247,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168,85,247,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
        }
        .bg-orb-1 { width:600px;height:600px;background:radial-gradient(circle,rgba(124,58,237,0.14),transparent 70%);top:-200px;left:-150px; }
        .bg-orb-2 { width:450px;height:450px;background:radial-gradient(circle,rgba(236,72,153,0.1),transparent 70%);bottom:-100px;right:-100px; }
        .bg-orb-3 { width:350px;height:350px;background:radial-gradient(circle,rgba(99,102,241,0.08),transparent 70%);top:50%;left:55%; }

        /* ===== LAYOUT ===== */
        .page-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== NAV ===== */
        .top-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(5,5,16,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(168,85,247,0.12);
        }
        .nav-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 2.25rem; height: 2.25rem;
            background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(236,72,153,0.2));
            border: 1.5px solid rgba(168,85,247,0.35);
            border-radius: 0.625rem;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-logo-text {
            font-size: 1.05rem;
            font-weight: 800;
            background: linear-gradient(135deg, #e2e8f0, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-login {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(124,58,237,0.25), rgba(236,72,153,0.15));
            border: 1.5px solid rgba(168,85,247,0.35);
            border-radius: 0.625rem;
            color: #c4b5fd;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-login:hover {
            background: linear-gradient(135deg, rgba(124,58,237,0.45), rgba(236,72,153,0.3));
            border-color: rgba(168,85,247,0.6);
            color: white;
            box-shadow: 0 4px 15px rgba(168,85,247,0.25);
        }

        /* ===== MAIN ===== */
        .main-content {
            flex: 1;
            padding: 2.5rem 1.5rem 4rem;
        }
        .container {
            max-width: 680px;
            margin: 0 auto;
        }

        /* ===== CARD ===== */
        .pcard {
            background: linear-gradient(135deg, rgba(15,10,40,0.97), rgba(10,5,30,0.98));
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 60px rgba(168,85,247,0.06);
        }
        .pcard::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #a855f7, #ec4899, transparent);
        }
        .pcard-body { padding: 2rem; }

        /* ===== FOOTER ===== */
        .page-footer {
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.78rem;
            color: rgba(100,116,139,0.7);
        }
        .page-footer span { color: #a855f7; font-weight: 700; }

        @yield('extra_styles')
    </style>
    @yield('styles')
    @livewireStyles
</head>
<body>
    <!-- Ambient -->
    <div class="bg-canvas">
        <div class="bg-grid"></div>
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
        <div class="bg-orb bg-orb-3"></div>
    </div>

    <div class="page-wrap">
        <!-- Nav -->
        <nav class="top-nav">
            <div class="nav-inner">
                @php
                    $siteName = \App\Models\Setting::get('site_name', 'Tu Código');
                    $siteLogo = \App\Models\Setting::get('site_logo', null);
                @endphp
                <a href="{{ route('public.query') }}" class="nav-logo">
                    <div class="nav-logo-icon">
                        @if($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($siteLogo) }}" alt="{{ $siteName }}" style="width:1.6rem;height:1.6rem;object-fit:contain;">
                        @else
                            <svg width="20" height="20" fill="none" stroke="url(#nGrad)" viewBox="0 0 24 24">
                                <defs><linearGradient id="nGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#a855f7"/><stop offset="100%" stop-color="#ec4899"/></linearGradient></defs>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <circle cx="12" cy="12" r="2" fill="#a855f7" stroke="none"/>
                            </svg>
                        @endif
                    </div>
                    <span class="nav-logo-text">{{ $siteName }}</span>
                </a>

                <a href="{{ route('login') }}" class="nav-login">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Panel de Cliente
                </a>
            </div>
        </nav>

        <!-- Main -->
        <main class="main-content">
            <div class="container">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <x-footer type="public" />
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
