@extends('install.layout')

@section('title', 'Administrador')
@section('subtitle', 'Paso 4: Cuenta de Administrador Maestro')

@php $step = 4; @endphp

@section('content')
<div>
    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 mb-6 flex gap-3">
        <svg class="w-6 h-6 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <p class="text-sm text-green-300 font-medium">¡Conexión a la base de datos exitosa! Ahora configura tu cuenta maestra.</p>
    </div>

    <form action="{{ route('install.process') }}" method="POST" class="space-y-5" onsubmit="document.getElementById('install-btn').classList.add('hidden'); document.getElementById('loading-btn').classList.remove('hidden');">
        @csrf
        
        <!-- Name -->
        <div class="space-y-1">
            <label class="block text-sm font-medium text-slate-300">Tu Nombre Completo</label>
            <input type="text" name="admin_name" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Ej. Luis Martinez">
        </div>

        <!-- Email -->
        <div class="space-y-1">
            <label class="block text-sm font-medium text-slate-300">Correo Electrónico (Para Iniciar Sesión)</label>
            <input type="email" name="admin_email" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="admin@nexuscode.com">
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300">Contraseña</label>
                <input type="password" name="admin_password" required minlength="8" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300">Confirmar Contraseña</label>
                <input type="password" name="admin_password_confirmation" required minlength="8" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex justify-between items-center pt-6 mt-6 border-t border-white/10">
            <a href="{{ route('install.database') }}" class="text-slate-400 hover:text-white transition text-sm">Volver</a>
            
            <button id="install-btn" type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg px-6 py-2.5 transition-all shadow-lg shadow-indigo-600/30">
                Instalar NexusCode
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </button>

            <!-- Loading State -->
            <button id="loading-btn" type="button" disabled class="hidden inline-flex items-center gap-2 bg-indigo-600/50 text-white font-medium rounded-lg px-6 py-2.5 cursor-wait">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Instalando...
            </button>
        </div>
    </form>
</div>
@endsection

