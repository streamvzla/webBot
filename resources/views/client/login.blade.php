<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Códigos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .glass-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(71, 85, 105, 0.5);
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center">
    @php
        $siteName = \App\Models\Setting::get('site_name', 'Sistema de Códigos');
        $siteLogo = \App\Models\Setting::get('site_logo', null);
    @endphp
    <div class="w-full max-w-md px-4">
        <!-- Logo/Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-yellow-400/20 mb-4">
                @if($siteLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siteLogo))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($siteLogo) }}" alt="{{ $siteName }}" class="w-16 h-16 object-contain">
                @else
                    <svg class="w-12 h-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                @endif
            </div>
            <h1 class="text-2xl font-bold text-white">{{ $siteName }}</h1>
            <p class="text-gray-400 mt-2">Inicia sesión para acceder a tus códigos</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-2xl p-8">
            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('client.login.post') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-600 text-white placeholder-gray-400 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition"
                        placeholder="tu@email.com">
                    @error('email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Contraseña</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-600 text-white placeholder-gray-400 focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 transition"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 rounded-lg transition">
                    Iniciar Sesión
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; {{ date('Y') }} {{ $siteName }}
        </p>
    </div>

    @if(session('account_suspended'))
        <script>
            Swal.fire({
                title: 'ACCESO RESTRINGIDO',
                text: '{{ session("account_suspended") }}',
                icon: 'error',
                background: 'rgba(15, 10, 24, 0.95)',
                color: '#ffffff',
                iconColor: '#f87171',
                confirmButtonColor: '#ec4899', // Pink
                confirmButtonText: 'Entendido',
                customClass: {
                    popup: 'border border-pink-500/40 rounded-2xl shadow-[0_0_40px_rgba(236,72,153,0.2)] backdrop-blur-xl',
                    title: 'font-bold text-xl tracking-wider',
                    confirmButton: 'px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-pink-500/50 transition-all duration-300'
                }
            });
        </script>
    @endif
</body>
</html>
