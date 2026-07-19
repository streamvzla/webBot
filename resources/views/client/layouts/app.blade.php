<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — Tu Código</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        /* Poppins en TODO el sistema — overrides cualquier página */
        *, *::before, *::after { font-family: 'Poppins', sans-serif !important; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #06040d;
            background-image:
                linear-gradient(rgba(255,255,255,0.012) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.012) 1px, transparent 1px);
            background-size: 50px 50px;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ═══════════════════════════════
           HEADER / TOP BAR
        ═══════════════════════════════ */
        .app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            height: 64px;
            background: rgba(6,4,13,0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(168,85,247,0.12);
            position: sticky;
            top: 0;
            z-index: 40;
            /* Línea degradada inferior */
            box-shadow: 0 1px 0 0 rgba(168,85,247,0.08), 0 4px 20px rgba(0,0,0,0.4);
        }
        /* Línea gradiente top del header */
        .app-header::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(168,85,247,0.4), rgba(236,72,153,0.2), transparent);
        }

        .header-left { display: flex; align-items: center; gap: 1rem; }
        .header-center { display: none; align-items: center; gap: 0.25rem; }
        @media(min-width: 768px) { .header-center { display: flex; } }
        .header-right { display: flex; align-items: center; gap: 0.875rem; }

        /* Hamburger */
        .hamburger-btn {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.625rem;
            width: 2.25rem; height: 2.25rem;
            display: flex; align-items: center; justify-content: center;
            color: rgba(148,163,184,0.7); cursor: pointer; transition: all 0.2s;
            flex-shrink: 0;
        }
        .hamburger-btn:hover {
            background: rgba(168,85,247,0.1);
            border-color: rgba(168,85,247,0.3);
            color: #c4b5fd;
            transform: scale(1.05);
        }
        @media(min-width: 768px) { .hamburger-btn { display: none; } }

        /* Logo */
        .logo-box {
            display: flex; align-items: center; gap: 0.625rem;
            text-decoration: none; color: white;
            flex-shrink: 0;
        }
        .logo-icon {
            width: 2.25rem; height: 2.25rem;
            background: linear-gradient(135deg, rgba(124,58,237,0.25), rgba(236,72,153,0.1));
            border: 1px solid rgba(168,85,247,0.35);
            border-radius: 0.625rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 12px rgba(168,85,247,0.2);
            transition: all 0.3s;
        }
        .logo-box:hover .logo-icon {
            background: linear-gradient(135deg, rgba(124,58,237,0.4), rgba(236,72,153,0.2));
            box-shadow: 0 0 20px rgba(168,85,247,0.4);
            transform: scale(1.05);
        }
        .logo-icon svg { color: #c4b5fd; }
        .logo-text {
            font-weight: 800; font-size: 1rem; letter-spacing: -0.03em;
            background: linear-gradient(135deg, #ffffff 0%, #c4b5fd 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Desktop nav links (center) */
        .nav-pill {
            display: flex; align-items: center; gap: 0.375rem;
            padding: 0.45rem 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.82rem; font-weight: 600;
            color: rgba(148,163,184,0.8);
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .nav-pill svg { width: 1rem; height: 1rem; opacity: 0.7; transition: opacity 0.2s; }
        .nav-pill:hover {
            color: #e2e8f0;
            background: rgba(255,255,255,0.04);
            border-color: rgba(255,255,255,0.07);
        }
        .nav-pill:hover svg { opacity: 1; }
        .nav-pill.active {
            color: #c4b5fd;
            background: rgba(168,85,247,0.1);
            border-color: rgba(168,85,247,0.25);
        }
        .nav-pill.active svg { opacity: 1; color: #a855f7; }

        /* User profile pill */
        .user-profile-btn {
            display: flex; align-items: center; gap: 0.625rem;
            background: rgba(255,255,255,0.025);
            border: 1px solid rgba(255,255,255,0.07);
            padding: 0.3rem 0.875rem 0.3rem 0.3rem;
            border-radius: 9999px;
            text-decoration: none; transition: all 0.25s; cursor: pointer;
        }
        .user-profile-btn:hover {
            border-color: rgba(168,85,247,0.35);
            background: rgba(168,85,247,0.07);
            box-shadow: 0 0 12px rgba(168,85,247,0.12);
        }
        .user-avatar {
            width: 2rem; height: 2rem; border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.85rem; color: white;
            flex-shrink: 0; overflow: hidden;
            box-shadow: 0 0 0 2px rgba(168,85,247,0.3);
        }
        .user-info { display: none; flex-direction: column; }
        @media(min-width: 480px) { .user-info { display: flex; } }
        .user-name { font-size: 0.78rem; font-weight: 700; color: white; line-height: 1.2; }

        /* Dropdown user menu */
        .user-menu-wrapper {
            position: relative;
        }
        .user-dropdown {
            position: absolute;
            top: calc(100% + 0.625rem);
            right: 0;
            width: 220px;
            background: rgba(10,6,25,0.97);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(168,85,247,0.2);
            border-radius: 1rem;
            padding: 0.5rem;
            box-shadow: 0 20px 50px rgba(0,0,0,0.7), 0 0 0 1px rgba(168,85,247,0.05);
            z-index: 100;
            opacity: 0;
            transform: translateY(-8px) scale(0.97);
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.16,1,0.3,1);
        }
        /* Línea gradiente top */
        .user-dropdown::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(168,85,247,0.5), rgba(236,72,153,0.3), transparent);
            border-radius: 1rem 1rem 0 0;
        }
        .user-dropdown.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        /* Mini header del dropdown */
        .dropdown-user-header {
            padding: 0.75rem 0.875rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 0.375rem;
        }
        .dropdown-user-name {
            font-size: 0.82rem; font-weight: 700; color: white;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .dropdown-user-sub {
            font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-top: 0.15rem;
        }
        /* Items del dropdown */
        .dropdown-item {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.82rem; font-weight: 600;
            color: rgba(148,163,184,0.9);
            text-decoration: none; cursor: pointer;
            border: 1px solid transparent; background: transparent;
            width: 100%; text-align: left;
            transition: all 0.2s;
        }
        .dropdown-item svg { width: 1rem; height: 1rem; flex-shrink: 0; }
        .dropdown-item:hover {
            color: white;
            background: rgba(255,255,255,0.04);
            border-color: rgba(255,255,255,0.07);
        }
        .dropdown-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
            margin: 0.375rem 0;
        }
        .dropdown-item.danger { color: rgba(248,113,113,0.8); }
        .dropdown-item.danger:hover {
            color: #f87171;
            background: rgba(239,68,68,0.08);
            border-color: rgba(239,68,68,0.2);
        }

        /* Online indicator dot */
        .online-dot {
            width: 0.5rem; height: 0.5rem; border-radius: 50%;
            background: #34d399; box-shadow: 0 0 6px rgba(52,211,153,0.7);
            flex-shrink: 0;
        }

        /* ═══════════════════════════════
           SIDEBAR DRAWER
        ═══════════════════════════════ */
        .sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
            z-index: 45; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active { opacity: 1; pointer-events: auto; }

        .sidebar-drawer {
            position: fixed; top: 0; left: 0; bottom: 0; width: 288px;
            background: rgba(6,4,13,0.97);
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border-right: 1px solid rgba(168,85,247,0.12);
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; flex-direction: column;
            box-shadow: 4px 0 40px rgba(0,0,0,0.6), 1px 0 0 rgba(168,85,247,0.08);
        }
        /* Línea gradiente lateral derecha del sidebar */
        .sidebar-drawer::after {
            content: '';
            position: absolute;
            top: 0; right: -1px; bottom: 0; width: 1px;
            background: linear-gradient(to bottom, transparent, rgba(168,85,247,0.4) 30%, rgba(236,72,153,0.2) 70%, transparent);
        }
        .sidebar-drawer.active { transform: translateX(0); }

        /* Sidebar header */
        .sidebar-header {
            padding: 1rem 1.25rem;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .close-sidebar {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            color: rgba(148,163,184,0.5); cursor: pointer;
            width: 2rem; height: 2rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 0.5rem; transition: all 0.2s;
        }
        .close-sidebar:hover { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); color: #f87171; }

        /* Sidebar section label */
        .sidebar-section-label {
            font-size: 0.62rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.12em; color: rgba(100,116,139,0.6);
            padding: 0.875rem 1.25rem 0.375rem;
        }

        /* Sidebar nav */
        .sidebar-nav {
            padding: 0.75rem 0.875rem;
            display: flex; flex-direction: column; gap: 0.125rem;
            flex: 1; overflow-y: auto;
        }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(168,85,247,0.2); border-radius: 9999px; }

        .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.7rem 0.875rem;
            border-radius: 0.75rem;
            color: rgba(148,163,184,0.85);
            text-decoration: none;
            font-weight: 600; font-size: 0.84rem;
            border: 1px solid transparent;
            background: transparent;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden;
        }
        .nav-link-icon {
            width: 2rem; height: 2rem; border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            flex-shrink: 0;
            transition: all 0.25s;
            color: rgba(148,163,184,0.6);
        }
        .nav-link-icon svg { width: 1rem; height: 1rem; }
        .nav-link-text { flex: 1; }

        .nav-link:hover {
            color: #e2e8f0;
            background: rgba(255,255,255,0.03);
            border-color: rgba(255,255,255,0.06);
            transform: translateX(4px);
        }
        .nav-link:hover .nav-link-icon {
            background: rgba(168,85,247,0.1);
            border-color: rgba(168,85,247,0.2);
            color: #c4b5fd;
        }

        .nav-link.active {
            color: white;
            background: linear-gradient(90deg, rgba(168,85,247,0.15) 0%, rgba(236,72,153,0.03) 100%);
            border-color: rgba(168,85,247,0.25);
            border-left-color: #a855f7;
            border-left-width: 2px;
        }
        .nav-link.active .nav-link-icon {
            background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(168,85,247,0.1));
            border-color: rgba(168,85,247,0.4);
            color: #a855f7;
            box-shadow: 0 0 8px rgba(168,85,247,0.2);
        }
        /* Shimmer on active link */
        .nav-link.active::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent, rgba(168,85,247,0.04), transparent);
        }

        /* Sidebar divider */
        .sidebar-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
            margin: 0.5rem 0.875rem;
        }

        /* Sidebar footer — user card */
        .sidebar-footer {
            padding: 0.875rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar-user-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 1rem;
            padding: 0.875rem 1rem;
            display: flex; align-items: center; gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .sidebar-avatar {
            width: 2.5rem; height: 2.5rem; border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1rem; color: white;
            flex-shrink: 0; overflow: hidden;
            box-shadow: 0 0 0 2px rgba(168,85,247,0.3), 0 0 12px rgba(168,85,247,0.2);
        }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name {
            font-size: 0.82rem; font-weight: 700; color: white;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-plan {
            font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar-online-row {
            display: flex; align-items: center; gap: 0.375rem;
            font-size: 0.62rem; font-weight: 600; color: #34d399;
            margin-top: 0.2rem;
        }
        .sidebar-online-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: #34d399; box-shadow: 0 0 5px rgba(52,211,153,0.7);
        }

        .btn-logout {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; padding: 0.625rem 1rem;
            border-radius: 0.75rem;
            background: rgba(239,68,68,0.06);
            border: 1px solid rgba(239,68,68,0.15);
            color: rgba(248,113,113,0.8); font-weight: 700; font-size: 0.8rem;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-logout svg { width: 1rem; height: 1rem; }
        .btn-logout:hover {
            background: rgba(239,68,68,0.14);
            border-color: rgba(239,68,68,0.35);
            color: #f87171;
            transform: translateY(-1px);
        }

        /* ═══════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════ */
        .app-main {
            flex: 1;
            padding: 2rem 1.5rem;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Global glass classes */
        .glass-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(168,85,247,0.12);
            border-radius: 1.25rem;
            position: relative; overflow: hidden;
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: rgba(168,85,247,0.25);
            background: rgba(168,85,247,0.04);
            box-shadow: 0 12px 40px rgba(168,85,247,0.08);
        }

        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.4s ease-out forwards; }

        /* ═══════════════════════════════
           FOOTER
        ═══════════════════════════════ */
        .page-footer {
            position: relative; z-index: 1;
            padding: 1rem 2rem;
            border-top: 1px solid rgba(168,85,247,0.08);
            background: rgba(6,4,13,0.7);
            backdrop-filter: blur(12px);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 0.75rem;
            margin-top: auto;
        }
        .footer-left { font-size: 0.7rem; color: rgba(100,116,139,0.45); }
        .footer-left strong {
            background: linear-gradient(135deg, #a855f7, #ec4899);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; font-weight: 700;
        }
        .footer-center { display: flex; align-items: center; gap: 0.5rem; font-size: 0.67rem; color: rgba(100,116,139,0.35); }
        .footer-right { display: flex; align-items: center; gap: 0.375rem; }
        .footer-status { width: 0.4rem; height: 0.4rem; border-radius: 50%; background: #34d399; box-shadow: 0 0 5px rgba(52,211,153,0.6); }
        .footer-ver { font-size: 0.65rem; color: rgba(100,116,139,0.4); font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
    </style>
    @yield('styles')
</head>
<body>

    {{-- ══ TOP HEADER ══ --}}
    <header class="app-header">
        <div class="header-left">
            {{-- Hamburger (mobile) --}}
            <button class="hamburger-btn" onclick="toggleSidebar()" aria-label="Menu">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h10M4 18h16"/>
                </svg>
            </button>

            {{-- Logo --}}
            <a href="{{ route('client.dashboard') }}" class="logo-box">
                <div class="logo-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="logo-text">Tu Código</span>
            </a>
        </div>

        {{-- Desktop Nav (center) --}}
        <nav class="header-center">
            <a href="{{ route('client.dashboard') }}" class="nav-pill {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
                Dashboard
            </a>
            <a href="{{ route('client.query') }}" class="nav-pill {{ request()->routeIs('client.query*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Consultar
            </a>
            <a href="{{ route('client.warranties.index') }}" class="nav-pill {{ request()->routeIs('client.warranties*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                Garantías
            </a>
            <a href="{{ route('client.guide') }}" class="nav-pill {{ request()->routeIs('client.guide') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Guía
            </a>
            <a href="{{ route('client.about') }}" class="nav-pill {{ request()->routeIs('client.about') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Acerca
            </a>
        </nav>

        {{-- Right side --}}
        <div class="header-right">
            <div class="online-dot" title="En línea"></div>
            @php $client = auth('client')->user(); @endphp
            {{-- Dropdown user menu --}}
            <div class="user-menu-wrapper" id="userMenuWrapper">
                {{-- Pill trigger --}}
                <button class="user-profile-btn" onclick="toggleUserMenu(event)" style="cursor:pointer;border:none;background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.07);" id="userMenuTrigger">
                    <div class="user-avatar">
                        @if($client->avatar)
                            <img src="{{ asset($client->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="Avatar">
                        @else
                            {{ strtoupper(substr($client->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ $client->name }}</span>
                    </div>
                    {{-- Chevron --}}
                    <svg id="userMenuChevron" style="width:0.875rem;height:0.875rem;color:rgba(148,163,184,0.5);transition:transform 0.2s;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div class="user-dropdown" id="userDropdown">
                    {{-- Header info --}}
                    <div class="dropdown-user-header">
                        <div class="dropdown-user-name">{{ $client->name }}</div>
                        <div class="dropdown-user-sub">Cliente Premium</div>
                    </div>

                    {{-- Links --}}
                    <a href="{{ route('client.profile') }}" class="dropdown-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                        Mi Perfil
                    </a>
                    <a href="{{ route('client.dashboard') }}" class="dropdown-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
                        Dashboard
                    </a>

                    <div class="dropdown-divider"></div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('client.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- ══ SIDEBAR OVERLAY ══ --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- ══ SIDEBAR DRAWER ══ --}}
    <aside class="sidebar-drawer" id="sidebarDrawer">

        {{-- Header --}}
        <div class="sidebar-header">
            <div class="logo-box">
                <div class="logo-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="logo-text">Tu Código</span>
            </div>
            <button class="close-sidebar" onclick="toggleSidebar()" aria-label="Cerrar menú">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <p class="sidebar-section-label">Principal</p>

            <a href="{{ route('client.dashboard') }}" class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                <div class="nav-link-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
                </div>
                <span class="nav-link-text">Dashboard</span>
            </a>

            <a href="{{ route('client.query') }}" class="nav-link {{ request()->routeIs('client.query*') ? 'active' : '' }}">
                <div class="nav-link-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </div>
                <span class="nav-link-text">Consultar Código</span>
            </a>

            <a href="{{ route('client.warranties.index') }}" class="nav-link {{ request()->routeIs('client.warranties.*') ? 'active' : '' }}">
                <div class="nav-link-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <span class="nav-link-text">Mis Garantías</span>
            </a>

            <div class="sidebar-divider"></div>
            <p class="sidebar-section-label">Recursos</p>

            <a href="{{ route('client.guide') }}" class="nav-link {{ request()->routeIs('client.guide') ? 'active' : '' }}">
                <div class="nav-link-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <span class="nav-link-text">Guía de Uso</span>
            </a>

            <a href="{{ route('client.about') }}" class="nav-link {{ request()->routeIs('client.about') ? 'active' : '' }}">
                <div class="nav-link-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <span class="nav-link-text">Acerca de</span>
            </a>

            <div class="sidebar-divider"></div>
            <p class="sidebar-section-label">Cuenta</p>

            <a href="{{ route('client.profile') }}" class="nav-link {{ request()->routeIs('client.profile*') ? 'active' : '' }}">
                <div class="nav-link-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                </div>
                <span class="nav-link-text">Mi Perfil</span>
            </a>
        </nav>

        {{-- User Card en el footer del sidebar (sin logout — está en el header) --}}
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="sidebar-avatar" style="overflow:hidden;">
                    @if($client->avatar)
                        <img src="{{ asset($client->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                    @else
                        {{ strtoupper(substr($client->name, 0, 1)) }}
                    @endif
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ $client->name }}</div>
                    <div class="sidebar-user-plan">Cliente Premium</div>
                    <div class="sidebar-online-row">
                        <div class="sidebar-online-dot"></div>
                        En línea
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ══ MAIN CONTENT ══ --}}
    <main class="app-main">
        @yield('content')
    </main>

    {{-- ══ FOOTER ══ --}}
    <x-footer type="client" />

    <x-alerts />

    <script>
        function toggleSidebar() {
            document.getElementById('sidebarDrawer').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
            document.body.style.overflow = document.getElementById('sidebarDrawer').classList.contains('active') ? 'hidden' : '';
        }

        function toggleUserMenu(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            const chevron  = document.getElementById('userMenuChevron');
            const isOpen   = dropdown.classList.contains('open');
            dropdown.classList.toggle('open');
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('userMenuWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('userDropdown').classList.remove('open');
                document.getElementById('userMenuChevron').style.transform = 'rotate(0deg)';
            }
        });

        // Cerrar sidebar con Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (document.getElementById('sidebarDrawer').classList.contains('active')) toggleSidebar();
                document.getElementById('userDropdown')?.classList.remove('open');
                if(document.getElementById('userMenuChevron')) document.getElementById('userMenuChevron').style.transform = 'rotate(0deg)';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
