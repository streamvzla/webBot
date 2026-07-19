<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador Tu-Codigo - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .install-bg {
            background-color: #020617; /* Solid very dark slate */
            background-image: radial-gradient(circle at top right, rgba(99, 102, 241, 0.05), transparent 50%),
                              radial-gradient(circle at bottom left, rgba(168, 85, 247, 0.05), transparent 50%);
        }
        .glass-panel {
            background: #0f172a; /* Solid slate-900 */
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="install-bg min-h-screen text-slate-100 flex items-center justify-center relative py-10">
    
    <!-- Flat decorative line top -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-600 to-emerald-500"></div>

    <div class="w-full max-w-2xl px-6 relative z-10 animate-fade-in-up">
        
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="mb-5 flex justify-center">
                <div class="w-20 h-20 bg-slate-900 rounded-2xl flex items-center justify-center shadow-[0_0_30px_rgba(99,102,241,0.3)] border border-indigo-500/30 relative group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all"></div>
                    <svg class="w-12 h-12 drop-shadow-[0_0_15px_rgba(99,102,241,0.8)] relative z-10" viewBox="0 0 24 24" fill="none" stroke="url(#nexus-gradient-install)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <defs>
                            <linearGradient id="nexus-gradient-install" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#818cf8" />
                                <stop offset="100%" stop-color="#c084fc" />
                            </linearGradient>
                        </defs>
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        <circle cx="12" cy="12" r="2" fill="#c084fc" stroke="none"></circle>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">Instalador Tu-Codigo</h1>
            <p class="text-slate-400 text-sm mt-2 font-light">@yield('subtitle', 'Asistente de Configuración')</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8 flex justify-between items-center relative">
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-slate-800 rounded-full z-0"></div>
            @php $currentStep = $step ?? 1; @endphp
            
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-indigo-500 rounded-full z-0 transition-all duration-500" style="width: {{ ($currentStep - 1) * 25 }}%"></div>

            @for ($i = 1; $i <= 5; $i++)
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold z-10 relative {{ $currentStep >= $i ? 'bg-indigo-500 text-white shadow-[0_0_10px_rgba(99,102,241,0.5)]' : 'bg-slate-800 text-slate-500' }}">
                    {{ $i }}
                </div>
            @endfor
        </div>

        <!-- Content Card -->
        <div class="glass-panel rounded-2xl p-8 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-start gap-3">
                    <svg class="w-[14px] h-[14px] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="text-center mt-8">
            <p class="text-xs text-slate-500 font-medium">
                Desarrollado con <span class="text-red-500">❤</span> por <span class="text-yellow-400 font-bold">Luis Martinez, desde Valencia-Venezuela</span><svg width="16" height="11" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:-2px;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,0.3);margin-left:4px;"><rect width="36" height="8" fill="#FFCC00"/><rect y="8" width="36" height="8" fill="#00247D"/><rect y="16" width="36" height="8" fill="#CF142B"/><g fill="#fff"><circle cx="11.5" cy="13.5" r="0.8"/><circle cx="13" cy="11.5" r="0.8"/><circle cx="15" cy="10" r="0.8"/><circle cx="17" cy="9.2" r="0.8"/><circle cx="19" cy="9.2" r="0.8"/><circle cx="21" cy="10" r="0.8"/><circle cx="23" cy="11.5" r="0.8"/><circle cx="24.5" cy="13.5" r="0.8"/></g></svg>
            </p>
        </footer>
    </div>


    <!-- Toast Notifications -->
    @if(session('success'))
    <div id="toast-success" class="fixed bottom-5 right-5 z-50 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-xl shadow-lg shadow-emerald-500/10 flex items-center gap-3 animate-fade-in-up transition-opacity duration-500">
        <div class="bg-emerald-500/20 p-2 rounded-full">
            <svg class="w-[14px] h-[14px] text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="font-medium">
            {{ session('success') }}
        </div>
        <button onclick="document.getElementById('toast-success').style.opacity='0'; setTimeout(()=>document.getElementById('toast-success').remove(), 500);" class="ml-4 text-emerald-500 hover:text-emerald-300">
            <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-success');
            if(toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
    @endif
</body>
</html>

