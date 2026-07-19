<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - Sistema de Verificación</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-bg { background: radial-gradient(circle at top left, #1e1b4b 0%, #0f172a 50%, #020617 100%); }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-premium {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .input-premium:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.25);
            outline: none;
        }
        .btn-premium {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Efectos de fondo -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-600/20 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-600/20 blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-md animate-fade-in-up z-10">
        <!-- Header / Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-slate-800 border border-slate-700/50 mb-4  shadow-xl">
                <svg class="w-10 h-10 text-indigo-400 drop-shadow-[0_0_8px_rgba(99,102,241,0.8)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Seguridad 2FA</h1>
            <p class="text-slate-400 mt-2">Protección adicional habilitada</p>
        </div>

        <!-- Formulario Glassmorphism -->
        <div class="glass-panel rounded-2xl p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
            
            <p class="text-slate-300 text-sm mb-6 text-center">
                Abre tu aplicación <span class="font-semibold text-white">Google Authenticator</span> e ingresa el código de 6 dígitos.
            </p>

            <form method="POST" action="{{ route('2fa.verify.post') }}" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-sm text-red-400">
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="code" class="block text-sm font-medium text-slate-300 mb-2 uppercase tracking-wider text-center">Código de Autenticación</label>
                    <input id="code" type="text" name="code" required autofocus autocomplete="one-time-code"
                        class="input-premium w-full px-4 py-4 rounded-xl text-center text-3xl font-bold tracking-[0.5em] placeholder-slate-600 focus:ring-2 focus:ring-indigo-500/50"
                        placeholder="••••••" maxlength="6" pattern="[0-9]*" inputmode="numeric">
                </div>

                <button type="submit" class="btn-premium w-full text-white font-semibold py-4 px-4 rounded-xl flex items-center justify-center gap-2 shadow-lg">
                    <span>Verificar Identidad</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition duration-300">
                    Cancelar e ir al login
                </a>
            </div>
        </div>
    </div>
</body>
</html>

