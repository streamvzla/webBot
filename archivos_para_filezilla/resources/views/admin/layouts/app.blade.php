<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TU CÓDIGO - Panel') - Sistema de Verificación</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/premium.css') }}?v={{ time() }}" rel="stylesheet">
    @yield('styles')
    @stack('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #000000; color: #ededed; }
        /* Custom scrollbar for enterprise feel */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        .glass-sidebar {
            background: #131314;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            width: 260px; /* Default width */
        }
        .glass-navbar {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-item-active {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .nav-item {
            color: #9ca3af;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.03);
            color: #fff;
        }
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        /* TOGGLE (Interruptor Custom Global) */
        .ui-toggle-wrap { position:relative;display:inline-flex;align-items:center;cursor:pointer; }
        .ui-toggle-inp { position:absolute;opacity:0;width:0;height:0; }
        .ui-toggle-track { width:2.75rem;height:1.5rem;background:rgba(255,255,255,0.1);border-radius:9999px;border:1px solid rgba(255,255,255,0.1);transition:all 0.3s;position:relative; }
        .ui-toggle-inp:checked ~ .ui-toggle-track { background:linear-gradient(135deg,#7c3aed,#a855f7);border-color:rgba(168,85,247,0.5);box-shadow:0 0 12px rgba(168,85,247,0.4); }
        .ui-toggle-thumb { position:absolute;top:2px;left:2px;width:1.1rem;height:1.1rem;background:white;border-radius:50%;transition:transform 0.3s;box-shadow:0 1px 4px rgba(0,0,0,0.4); }
        .ui-toggle-inp:checked ~ .ui-toggle-track .ui-toggle-thumb { transform:translateX(1.25rem); }

    </style>
</head>
<body class="antialiased overflow-hidden">
    
    <div class="flex h-screen relative z-10 w-full">
        
        <!-- Overlay General -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 transition-opacity" style="display: none;" onclick="document.getElementById('sidebar').style.transform = 'translateX(-100%)'; this.style.display = 'none';"></div>

        <!-- SIDEBAR ENTERPRISE (Overlay Drawer) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 glass-sidebar transition-all duration-300 flex flex-col" style="transform: translateX(-100%);">
            
            <!-- HEADER SIDEBAR -->
            <div class="h-16 flex items-center justify-between px-4 shrink-0 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center shadow-lg shadow-purple-500/20 shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <div class="flex flex-col sidebar-header-text">
                        <span class="font-bold text-sm tracking-tight text-white leading-none">TU CÓDIGO</span>
                        <span class="font-medium text-purple-400 uppercase tracking-widest mt-1" style="font-size: 9px;">Enterprise</span>
                    </div>
                </div>
                <!-- Close Button -->
                <button onclick="document.getElementById('sidebar').style.transform = 'translateX(-100%)'; document.getElementById('sidebar-overlay').style.display = 'none';" class="p-1.5 rounded-md text-gray-400 hover:text-white hover:bg-white/10 transition-colors shrink-0" title="Cerrar Menú">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- NAVEGACIÓN -->
            <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-1">
                @php
                    function navLink($route, $label, $icon, $isActive) {
                        $class = $isActive ? 'nav-item-active' : 'nav-item';
                        return "<a wire:navigate href=\"$route\" class=\"flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-medium $class nav-item-container\" title=\"$label\">
                                    <span class=\"opacity-90 shrink-0 nav-item-icon-wrapper\">$icon</span>
                                    <span class=\"sidebar-text whitespace-nowrap\">$label</span>
                                </a>";
                    }
                    $currentUser = auth()->user();
                @endphp

                {!! navLink(route('admin.dashboard'), 'Dashboard', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>', request()->routeIs('admin.dashboard')) !!}

                @if($currentUser && $currentUser->role !== 'user')
                    <div class="pt-3 pb-1 px-3 sidebar-section-title"><p class="font-semibold text-gray-500 uppercase tracking-wider" style="font-size: 10px;">Gestión</p></div>
                    {!! navLink(route('admin.platforms.index'), 'Plataformas', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>', request()->routeIs('admin.platforms.*')) !!}
                    {!! navLink(route('admin.servers.index'), 'Servidores', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2v-4a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>', request()->routeIs('admin.servers.*')) !!}
                    {!! navLink(route('admin.allowed-emails.index'), 'Correos', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>', request()->routeIs('admin.allowed-emails.*')) !!}
                @endif

                <div class="pt-3 pb-1 px-3 sidebar-section-title"><p class="font-semibold text-gray-500 uppercase tracking-wider" style="font-size: 10px;">Operaciones</p></div>
                {!! navLink(route('admin.queries.index'), 'Registros', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>', request()->routeIs('admin.queries.*')) !!}
                
                @if($currentUser && (in_array($currentUser->role, ['admin', 'user']) || $currentUser->id === 1))
                    @php $teamLabel = $currentUser->id === 1 ? 'Franquicias' : ($currentUser->role === 'admin' ? 'Revendedores' : 'Mi Equipo'); @endphp
                    {!! navLink(route('admin.users.index'), $teamLabel, '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>', request()->routeIs('admin.users.*')) !!}
                @endif

                @if($currentUser->role !== 'user')
                    {!! navLink(route('admin.franchise-plans.index'), 'Planes', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2zm12-3c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2zM9 10l12-3"/></svg>', request()->routeIs('admin.franchise-plans.*')) !!}
                @endif

                {!! navLink(route('admin.clients.index'), 'Clientes', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>', request()->routeIs('admin.clients.*')) !!}
                {!! navLink(route('admin.inventory.index'), 'Mi Inventario', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>', request()->routeIs('admin.inventory.*')) !!}
                {!! navLink(route('admin.query.index'), 'Consultar', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>', request()->routeIs('admin.query.*')) !!}
                {!! navLink(route('admin.warranties.index'), 'Garantías', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>', request()->routeIs('admin.warranties.*')) !!}

                @if($currentUser->id === 1)
                    <div class="pt-3 pb-1 px-3 sidebar-section-title"><p class="font-semibold text-purple-400 uppercase tracking-wider" style="font-size: 10px;">Súper Admin</p></div>
                    {!! navLink(route('admin.licenses.index'), 'Licencias', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>', request()->routeIs('admin.licenses.*')) !!}
                    {!! navLink(route('admin.ip-bans.index'), 'Anti-Spam', '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>', request()->routeIs('admin.ip-bans.*')) !!}
                @endif
            </nav>

            <div class="p-4 border-t border-white/5 shrink-0">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg bg-white/5 hover:bg-red-500/10 text-gray-400 hover:text-red-400 transition-colors text-xs font-medium border border-transparent hover:border-red-500/20 nav-item-container" title="Cerrar Sesión">
                        <svg class="w-4 h-4 shrink-0 nav-item-icon-wrapper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="sidebar-text">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 flex flex-col min-h-screen overflow-hidden relative">
            
            <!-- NAVBAR SUPERIOR -->
            <header class="h-16 glass-navbar flex items-center justify-between px-4 lg:px-6 z-30 shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Hamburger to open Sidebar -->
                    <button onclick="document.getElementById('sidebar').style.transform = 'translateX(0)'; document.getElementById('sidebar-overlay').style.display = 'block';" class="p-2 rounded-md text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="sm:block">
                        <h2 class="text-sm font-semibold text-white tracking-tight">@yield('header', 'Dashboard')</h2>
                    </div>
                </div>
                
                <div class="hidden md:flex flex-1 max-w-md mx-6"><livewire:admin.global-search /></div>
                
                <div class="flex items-center gap-4">
                    <!-- Status -->
                    <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        <span class="font-semibold text-emerald-400 uppercase tracking-widest" style="font-size: 9px;">En Vivo</span>
                    </div>
                    
                    <div class="w-[1px] h-5 bg-white/10 hidden sm:block"></div>
                    
                    <!-- User Menu -->
                    <div class="relative" id="userMenuWrapper">
                        <button onclick="toggleUserMenu(event)" class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full border border-transparent hover:bg-white/5 transition-all">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-xs font-bold text-white shadow-inner">{{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username ?? 'A', 0, 1)) }}</div>
                            <svg id="userMenuChevron" class="w-3.5 h-3.5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="userDropdown" class="absolute right-0 mt-2 w-56 bg-[#0F0F13] border border-white/10 rounded-xl shadow-2xl transition-all duration-200 z-50 origin-top-right" style="display: none;">
                            <div class="p-4 border-b border-white/5">
                                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? auth()->user()->username }}</p>
                                <p class="text-xs text-gray-400 mt-1 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="p-2">
                                <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Mi Perfil</a>
                                @if(isset(auth()->user()->role) && auth()->user()->role === 'admin')
                                <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="15" cy="12" r="3"/></svg> Configuración</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENIDO INTERNO -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-6" id="main-content-scroll">
                @if(session('read_only_mode'))
                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-6 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h3 class="text-red-300 font-semibold text-sm">Modo de Solo Lectura</h3>
                            <p class="text-red-200/70 text-xs mt-1">Tu suscripción ha expirado. El servicio para clientes está bloqueado.</p>
                        </div>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <x-alerts />

    <script>
        // Lógica Sidebar Toggle
        // Eliminada la función global toggleSidebar() ya que se maneja in-line para evitar problemas con Livewire

        // Lógica Menú de Usuario
        function toggleUserMenu(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            const chevron = document.getElementById('userMenuChevron');
            if (!dropdown) return;
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
                if(chevron) chevron.classList.add('rotate-180');
            } else {
                dropdown.style.display = 'none';
                if(chevron) chevron.classList.remove('rotate-180');
            }
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const chevron = document.getElementById('userMenuChevron');
            const wrapper = document.getElementById('userMenuWrapper');
            if (dropdown && dropdown.style.display === 'block' && wrapper && !wrapper.contains(e.target)) {
                dropdown.style.display = 'none';
                if(chevron) chevron.classList.remove('rotate-180');
            }
        });
        
        // Swal Confirm Global Premium
        window.swalConfirm = function(opts) {
            const isDanger = opts.danger !== false;
            return Swal.fire({
                title: opts.title || '¿Estás seguro?',
                html: opts.text || 'Esta acción no se puede deshacer.',
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: opts.confirmText || (isDanger ? 'Sí, eliminar' : 'Confirmar'),
                cancelButtonText: opts.cancelText || 'Cancelar',
                customClass: { popup: 'swal2-confirm-premium' + (isDanger ? ' swal2-confirm-danger' : ''), backdrop: 'swal2-backdrop-premium' }
            }).then(result => {
                if (result.isConfirmed && typeof opts.callback === 'function') opts.callback();
                return result.isConfirmed;
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('submit', function(e) {
                const form = e.target;
                if (!form.dataset.confirm) return;
                e.preventDefault();
                swalConfirm({
                    title: form.dataset.confirmTitle, text: form.dataset.confirm, confirmText: form.dataset.confirmBtn,
                    danger: form.dataset.confirmDanger !== 'false',
                    callback: () => { form.removeAttribute('data-confirm'); form.submit(); }
                });
            }, true);
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-confirm]');
                if (!btn || btn.tagName === 'FORM' || btn.form || btn.dataset.confirmForm) return;
                e.preventDefault();
                swalConfirm({
                    title: btn.dataset.confirmTitle, text: btn.dataset.confirm, confirmText: btn.dataset.confirmBtn,
                    danger: btn.dataset.confirmDanger !== 'false',
                    callback: () => { if (btn.onclick) btn.onclick(); }
                });
            }, true);
        });
    </script>
    @stack('scripts')
</body>
</html>
