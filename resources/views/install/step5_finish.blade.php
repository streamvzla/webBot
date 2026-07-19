@extends('install.layout')

@section('title', 'Finalizado')
@section('subtitle', 'Paso 5: Instalación Completada')

@php $step = 5; @endphp

@section('content')
<div class="text-center">
    <div class="w-20 h-20 bg-green-500/20 border border-green-500/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_30px_rgba(34,197,94,0.3)]">
        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>

    <h2 class="text-2xl font-bold mb-4 text-white">¡NexusCode Instalado con Éxito!</h2>
    
    <p class="text-slate-300 mb-8 leading-relaxed">
        Tu sistema está listo para operar. Hemos creado la base de datos, configurado las credenciales y asegurado el entorno. Por seguridad, el instalador ha sido bloqueado automáticamente.
    </p>

    <div class="bg-slate-900 border border-slate-700 rounded-xl p-5 mb-8 text-left max-w-sm mx-auto">
        <h4 class="font-medium text-slate-200 mb-3 text-center border-b border-white/10 pb-3">Siguientes Pasos</h4>
        <ul class="space-y-3">
            <li class="flex items-start gap-3">
                <span class="w-6 h-6 rounded bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold shrink-0">1</span>
                <span class="text-sm text-slate-300">Inicia sesión con tu nueva cuenta de administrador.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="w-6 h-6 rounded bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                <span class="text-sm text-slate-300">Ve a <strong>Configuración</strong> y sube el logo de tu empresa.</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="w-6 h-6 rounded bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                <span class="text-sm text-slate-300">Agrega tus primeros <strong>Servidores IMAP</strong>.</span>
            </li>
        </ul>
    </div>

    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg px-8 py-3 transition-all shadow-lg shadow-indigo-600/30">
        Ir al Panel de Inicio
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
    </a>
</div>
@endsection

