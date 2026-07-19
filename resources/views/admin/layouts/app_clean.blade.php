<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración') - Sistema de Verificación</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
    @stack('styles')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @php
        $colorPrimary = \App\Models\Setting::get('theme_color_primary', '#6366f1');
        $colorSecondary = \App\Models\Setting::get('theme_color_secondary', '#8b5cf6');
        $bgStart = \App\Models\Setting::get('theme_bg_start', '#1e1b4b');
        $bgEnd = \App\Models\Setting::get('theme_bg_end', '#020617');
    @endphp
</head>
<body class="gradient-bg min-h-screen text-white">
    <div class="ambient-light"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="sidebar-overlay hidden fixed inset-0 bg-black/50 z-30" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar mobile-hidden fixed top-0 left-0 h-full z-40 w-64 lg:static lg:flex lg:flex-col">
            @php
                $siteName = \App\Models\Setting::get('site_name', 'WinicSistem');
                $siteLogo = \App\Models\Setting::get('site_logo', null);
            @endphp
                    <div style="padding:1rem;position:relative;overflow:hidden;border-bottom:1px solid rgba(168,85,247,0.1);">
                        <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(168,85,247,0.6),rgba(236,72,153,0.3),transparent);"></div>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            @if($siteLogo && Storage::disk('public')->exists($siteLogo))
                                <img src="{{ Storage::url($siteLogo) }}" alt="{{ $siteName }}" style="width:2.25rem;height:2.25rem;object-fit:contain;border-radius:0.5rem;">
                            @else
                                <div style="width:2.5rem;height:2.5rem;flex-shrink:0;background:linear-gradient(135deg,rgba(124,58,237,0.2),rgba(236,72,153,0.1));border:1px solid rgba(168,85,247,0.35);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;box-shadow:0 0 16px rgba(168,85,247,0.2);">
                                    <svg style="width:1.15rem;height:1.15rem;" viewBox="0 0 24 24" fill="none" stroke="url(#sg-a)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><defs><linearGradient id="sg-a" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#818cf8"/><stop offset="100%" stop-color="#c084fc"/></linearGradient></defs><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><circle cx="12" cy="12" r="2" fill="#a855f7" stroke="none"/></svg>
                                </div>
                            @endif
                            <div style="min-width:0;flex:1;">
                                <p style="font-weight:800;font-size:0.9rem;background:linear-gradient(135deg,#f1f5f9,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $siteName }}</p>
                                <div style="display:flex;align-items:center;gap:0.35rem;margin-top:0.2rem;">
                                    <span style="width:0.45rem;height:0.45rem;background:#34d399;border-radius:50%;display:inline-block;box-shadow:0 0 6px rgba(52,211,153,0.7);animation:pulse 2s infinite;"></span>
                                    <span style="font-size:0.6rem;color:rgba(100,116,139,0.65);font-weight:600;letter-spacing:0.07em;text-transform:uppercase;">Sistema Activo &middot; v3.0 Enterprise</span>
                                </div>
                            </div>
                        </div>
                    </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a wire:navigate href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>

                @php $currentUser = auth()->user(); @endphp

                @if($currentUser && $currentUser->role !== 'user')
                <a wire:navigate href="{{ route('admin.platforms.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.platforms.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Plataformas</span>
                </a>

                <a wire:navigate href="{{ route('admin.servers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.servers.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2v-4a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    <span>Servidores</span>
                </a>

                <a wire:navigate href="{{ route('admin.allowed-emails.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.allowed-emails.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Correos</span>
                </a>
                @endif

                <a wire:navigate href="{{ route('admin.queries.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.queries.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>Registros</span>
                </a>


                @if($currentUser && (in_array($currentUser->role, ['admin', 'user']) || $currentUser->id === 1))
                <a wire:navigate href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>
                        @if($currentUser->id === 1)
                            Franquicias & Staff
                        @elseif($currentUser->role === 'admin')
                            Revendedor & Staff
                        @else
                            Mi Equipo
                        @endif
                    </span>
                </a>
                @endif

                @if($currentUser->role !== 'user')

                <a wire:navigate href="{{ route('admin.franchise-plans.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.franchise-plans.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Planes</span>
                </a>
                @endif

                <a wire:navigate href="{{ route('admin.clients.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.clients.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Clientes</span>
                </a>

                <a wire:navigate href="{{ route('admin.inventory.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.inventory.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>Mi Inventario</span>
                </a>
                
                <a wire:navigate href="{{ route('admin.query.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.query.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Consultar Código</span>
                </a>
                
                <a wire:navigate href="{{ route('admin.warranties.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.warranties.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Garantías</span>
                </a>

                @if(auth()->user()->id === 1)
                <div style="margin:1rem 0 0.35rem;padding:0 0.5rem;">
                    <div style="display:flex;align-items:center;gap:0.5rem;padding:0 0.5rem;">
                        <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(168,85,247,0.3),transparent);"></div>
                        <span style="font-size:0.6rem;font-weight:800;color:rgba(168,85,247,0.6);text-transform:uppercase;letter-spacing:0.1em;white-space:nowrap;">&#9670; Super Admin</span>
                        <div style="flex:1;height:1px;background:linear-gradient(90deg,transparent,rgba(168,85,247,0.3));"></div>
                    </div>
                </div>
                <a wire:navigate href="{{ route('admin.licenses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.licenses.*') ? 'nav-active' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Gestor de Licencias</span>
                </a>
                @endif

                @if(auth()->id() === 1)
                <a wire:navigate href="{{ route('admin.ip-bans.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.ip-bans.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Anti-Spam</span>
                </a>
                @endif

                @if($currentUser && $currentUser->role === 'admin')

                <a wire:navigate href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Configuración</span>
                </a>
                @endif
                
                <a wire:navigate href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.profile.*') ? 'bg-violet-500/10 text-violet-400 border border-violet-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Mi Perfil</span>
                </a>
            </nav>

            <div style="padding:0.75rem;border-top:1px solid rgba(168,85,247,0.1);">
                <div style="background:linear-gradient(135deg,rgba(15,10,40,0.8),rgba(10,5,25,0.9));border:1px solid rgba(168,85,247,0.12);border-radius:0.875rem;padding:0.75rem;margin-bottom:0.5rem;">
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div style="width:2rem;height:2rem;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#ec4899);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.75rem;font-weight:800;color:white;box-shadow:0 0 12px rgba(124,58,237,0.4);">
                            {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username ?? 'A', 0, 1)) }}
                        </div>
                        <div style="min-width:0;flex:1;">
                            <p style="font-size:0.78rem;font-weight:700;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name ?? auth()->user()->username }}</p>
                            <span style="font-size:0.6rem;font-weight:700;background:linear-gradient(135deg,rgba(124,58,237,0.2),rgba(236,72,153,0.1));border:1px solid rgba(168,85,247,0.25);border-radius:0.25rem;padding:0.05rem 0.35rem;color:#c4b5fd;text-transform:uppercase;letter-spacing:0.06em;">
                                @if(auth()->user()->id === 1) &#9670; Super Admin @elseif(isset(auth()->user()->role) && auth()->user()->role === 'admin') Admin @else Staff @endif
                            </span>
                        </div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.6rem 1rem;border-radius:0.75rem;border:1px solid rgba(239,68,68,0.15);background:rgba(239,68,68,0.06);color:rgba(248,113,113,0.8);font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.12)';this.style.color='#f87171';" onmouseout="this.style.background='rgba(239,68,68,0.06)';this.style.color='rgba(248,113,113,0.8)';">
                        <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-auto w-full min-h-screen">
            <!-- Mobile Header -->
            <header class="bg-slate-900  border-b border-white/10 p-4 sticky top-0 z-20 lg:hidden">
                <div class="flex items-center justify-between">
                    <button onclick="toggleSidebar()" class="text-gray-300 hover:text-white p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        @if($siteLogo && Storage::disk('public')->exists($siteLogo))
                            <img src="{{ Storage::url($siteLogo) }}" alt="{{ $siteName }}" class="w-14 h-14 object-contain">
                        @else
                            <svg class="w-8 h-8 drop-shadow-[0_0_8px_rgba(168,85,247,0.7)]" viewBox="0 0 24 24" fill="none" stroke="url(#mob-grad)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <defs><linearGradient id="mob-grad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#818cf8"/><stop offset="50%" stop-color="#a855f7"/><stop offset="100%" stop-color="#ec4899"/></linearGradient></defs>
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                                <circle cx="12" cy="12" r="2" fill="#a855f7" stroke="none"/>
                            </svg>
                        @endif
                        <span class="font-bold text-violet-400">{{ $siteName }}</span>
                    </div>
                    <div class="w-10"></div> <!-- Spacer for centering -->
                </div>
            </header>

            <!-- Desktop Header Enterprise -->
            <header class="hidden lg:block sticky top-0 z-10" style="padding:0 1.75rem;height:4rem;">
                <div style="height:100%;display:flex;align-items:center;justify-content:space-between;">
                    <!-- Page title -->
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div>
                            <h2 style="font-size:1.05rem;font-weight:800;background:linear-gradient(135deg,#f1f5f9 0%,#c4b5fd 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1.2;">@yield('header', 'Dashboard')</h2>
                            <p style="font-size:0.72rem;color:rgba(100,116,139,0.65);margin-top:0.1rem;">@yield('description', 'Panel de administración')</p>
                        </div>
                    </div>
                    
                    <livewire:admin.global-search />

                    <!-- Right controls -->
                    <div style="display:flex;align-items:center;gap:0.875rem;">
                        <!-- System status -->
                        <div style="display:flex;align-items:center;gap:0.4rem;padding:0.35rem 0.75rem;background:rgba(52,211,153,0.07);border:1px solid rgba(52,211,153,0.18);border-radius:9999px;">
                            <span style="width:0.45rem;height:0.45rem;background:#34d399;border-radius:50%;display:block;box-shadow:0 0 6px rgba(52,211,153,0.8);"></span>
                            <span style="font-size:0.65rem;font-weight:700;color:#34d399;text-transform:uppercase;letter-spacing:0.07em;">Sistema Online</span>
                        </div>
                        <!-- Divider -->
                        <div style="width:1px;height:1.5rem;background:rgba(168,85,247,0.15);"></div>
                        <!-- User chip Dropdown -->
                        <div class="user-menu-wrapper" id="userMenuWrapper">
                            <button onclick="toggleUserMenu(event)" id="userMenuTrigger" style="display:flex;align-items:center;gap:0.5rem;padding:0.3rem 0.75rem 0.3rem 0.35rem;background:linear-gradient(135deg,rgba(15,10,40,0.8),rgba(10,5,25,0.9));border:1px solid rgba(168,85,247,0.15);border-radius:9999px;cursor:pointer;transition:all 0.2s;outline:none;" onmouseover="this.style.borderColor='rgba(168,85,247,0.35)'" onmouseout="this.style.borderColor='rgba(168,85,247,0.15)'">
                                <div style="width:1.75rem;height:1.75rem;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#ec4899);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:800;color:white;box-shadow:0 0 10px rgba(124,58,237,0.4);flex-shrink:0;">
                                    {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username ?? 'A', 0, 1)) }}
                                </div>
                                <div style="text-align:left;">
                                    <p style="font-size:0.78rem;font-weight:700;color:#e2e8f0;line-height:1;">{{ auth()->user()->name ?? auth()->user()->username }}</p>
                                    <p style="font-size:0.6rem;color:rgba(196,181,253,0.7);margin-top:0.1rem;text-transform:uppercase;letter-spacing:0.05em;">
                                        @if(auth()->user()->id === 1)
                                            Super Admin
                                        @elseif(isset(auth()->user()->role))
                                            {{ ucfirst(auth()->user()->role) }}
                                        @else
                                            Staff
                                        @endif
                                    </p>
                                </div>
                                <svg id="userMenuChevron" style="width:0.875rem;height:0.875rem;color:rgba(148,163,184,0.5);transition:transform 0.2s;flex-shrink:0;margin-left:0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div class="user-dropdown" id="userDropdown">
                                <div class="dropdown-user-header">
                                    <div class="dropdown-user-name">{{ auth()->user()->name ?? auth()->user()->username }}</div>
                                    <div class="dropdown-user-sub">
                                        @if(auth()->user()->id === 1)
                                            Super Admin
                                        @elseif(isset(auth()->user()->role))
                                            {{ ucfirst(auth()->user()->role) }}
                                        @else
                                            Staff
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                                    Mi Perfil
                                </a>
                                @if(isset(auth()->user()->role) && auth()->user()->role === 'admin')
                                <a href="{{ route('admin.settings') }}" class="dropdown-item">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                    Configuración
                                </a>
                                @endif

                                <div class="dropdown-divider"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-4 lg:p-6 pb-20 lg:pb-10 flex-1">
                @if(session('read_only_mode'))
                    <div style="background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(220,38,38,0.05)); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.875rem; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 4px 20px rgba(239,68,68,0.1);">
                        <div style="background: rgba(239,68,68,0.2); width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(239,68,68,0.4);">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 style="color: #fca5a5; font-size: 1.05rem; font-weight: 800; margin-bottom: 0.35rem; letter-spacing: 0.02em;">Servicio Suspendido - Modo de Solo Lectura</h3>
                            <p style="color: rgba(254,226,226,0.85); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">La suscripción de tu Franquicia ha expirado. Por el momento, el panel se encuentra en modo de lectura y <strong style="color: #fecaca;">el servicio para tus clientes está bloqueado</strong>. Por favor, regulariza el pago para reactivar todas las funciones.</p>
                        </div>
                    </div>
                @endif
                @if(auth()->check() && auth()->user()->role === 'admin' && auth()->user()->subscription_ends_at && auth()->user()->subscription_ends_at > now() && auth()->user()->getDaysUntilExpiration() <= 5)
                    @php $daysLeft = auth()->user()->getDaysUntilExpiration(); @endphp
                    <div style="background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(217,119,6,0.05)); border: 1px solid rgba(245,158,11,0.3); border-radius: 0.875rem; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 4px 20px rgba(245,158,11,0.1);">
                        <div style="background: rgba(245,158,11,0.2); width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(245,158,11,0.4);">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 style="color: #fcd34d; font-size: 1.05rem; font-weight: 800; margin-bottom: 0.35rem; letter-spacing: 0.02em;">Suscripción por Vencer</h3>
                            <p style="color: rgba(254,243,199,0.85); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">¡Atención! Tu suscripción de franquicia vence en <strong style="color: #fde68a;">{{ $daysLeft }} día(s)</strong>. Por favor, contacta a soporte para renovar y evitar la suspensión del servicio.</p>
                        </div>
                    </div>
                @endif
                @yield('content')
            </div>

            <x-footer type="admin" />
        </main>
    </div>

    <x-alerts />

    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;

            // Only toggle on mobile
            if (window.innerWidth < 1024) {
                if (sidebar.classList.contains('mobile-hidden')) {
                    sidebar.classList.remove('mobile-hidden');
                    overlay.classList.remove('hidden');
                    body.classList.add('mobile-menu-open');
                } else {
                    sidebar.classList.add('mobile-hidden');
                    overlay.classList.add('hidden');
                    body.classList.remove('mobile-menu-open');
                }
            }
        }

        // Close sidebar when clicking overlay
        document.getElementById('sidebar-overlay')?.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking a link on mobile
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                const body = document.body;
                sidebar.classList.remove('mobile-hidden');
                overlay.classList.add('hidden');
                body.classList.remove('mobile-menu-open');
            }
        });

        // Dropdown toggle function
        function toggleUserMenu(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            const chevron  = document.getElementById('userMenuChevron');
            if(!dropdown) return;
            const isOpen   = dropdown.classList.contains('open');
            dropdown.classList.toggle('open');
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('userMenuWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const dropdown = document.getElementById('userDropdown');
                if(dropdown) dropdown.classList.remove('open');
                const chevron = document.getElementById('userMenuChevron');
                if(chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });

        // Close sidebar and dropdown with Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (window.innerWidth < 1024 && !document.getElementById('sidebar').classList.contains('mobile-hidden')) {
                    toggleSidebar();
                }
                const dropdown = document.getElementById('userDropdown');
                if(dropdown) dropdown.classList.remove('open');
                const chevron = document.getElementById('userMenuChevron');
                if(chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
    </script>

    <!-- ===== SWAL CONFIRM GLOBAL SYSTEM ===== -->

    <script>
    /**
     * swalConfirm — Global premium confirmation modal
     * @param {Object} opts - { title, text, confirmText, cancelText, danger, callback }
     */
    window.swalConfirm = function(opts) {
        const isDanger = opts.danger !== false; // default danger=true for deletions
        return Swal.fire({
            title: opts.title || '¿Estás seguro?',
            html: opts.text || 'Esta acción no se puede deshacer.',
            icon: isDanger ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: `<svg style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="${isDanger ? 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' : 'M5 13l4 4L19 7'}"/></svg> ${opts.confirmText || (isDanger ? 'Sí, eliminar' : 'Confirmar')}`,
            cancelButtonText: opts.cancelText || 'Cancelar',
            customClass: {
                popup: 'swal2-confirm-premium' + (isDanger ? ' swal2-confirm-danger' : ''),
                backdrop: 'swal2-backdrop-premium',
            },
            showClass: { popup: 'swal2-confirm-premium swal2-show' },
            hideClass: { popup: 'swal2-confirm-premium swal2-hide' },
            reverseButtons: false,
        }).then(result => {
            if (result.isConfirmed && typeof opts.callback === 'function') {
                opts.callback();
            }
            return result.isConfirmed;
        });
    };

    /**
     * Auto-intercept: forms with data-confirm attribute
     * Usage: <form data-confirm="¿Eliminar esto?" data-confirm-title="Eliminar Plataforma">
     */
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form.dataset.confirm) return;

            e.preventDefault();
            const title  = form.dataset.confirmTitle || '¿Confirmar acción?';
            const text   = form.dataset.confirm;
            const btnTxt = form.dataset.confirmBtn || undefined;
            const isDanger = form.dataset.confirmDanger !== 'false';

            swalConfirm({
                title, text,
                confirmText: btnTxt,
                danger: isDanger,
                callback: () => {
                    // Remove the listener to avoid loop, then submit
                    form.removeAttribute('data-confirm');
                    form.submit();
                }
            });
        }, true);

        /**
         * Auto-intercept: buttons/links with data-confirm attribute
         * Usage: <button type="button" data-confirm="¿Seguro?" data-confirm-form="formId">
         */
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-confirm]');
            if (!btn || btn.tagName === 'FORM') return;
            if (btn.form || btn.dataset.confirmForm) return; // handled by submit above

            e.preventDefault();
            const formId = btn.dataset.confirmForm;
            const title  = btn.dataset.confirmTitle || '¿Confirmar acción?';
            const text   = btn.dataset.confirm;
            const btnTxt = btn.dataset.confirmBtn || undefined;
            const isDanger = btn.dataset.confirmDanger !== 'false';

            swalConfirm({
                title, text,
                confirmText: btnTxt,
                danger: isDanger,
                callback: () => {
                    if (formId) {
                        document.getElementById(formId)?.submit();
                    } else if (btn.onclick) {
                        btn.onclick();
                    }
                }
            });
        }, true);
    });
    </script>

    @stack('scripts')
</body>
</html>



