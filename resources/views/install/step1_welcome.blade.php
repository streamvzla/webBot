@extends('install.layout')

@section('title', 'Bienvenido')
@section('subtitle', 'Paso 1: Bienvenida al Instalador')

@php $step = 1; @endphp

@section('content')
<div class="text-center">
    <h2 class="text-2xl font-semibold mb-4 text-white">¡Bienvenido a NexusCode!</h2>
    <p class="text-slate-300 mb-6 leading-relaxed">
        Estás a punto de instalar la plataforma de verificación de códigos más avanzada del mercado. 
        Este asistente configurará tu base de datos y preparará tu entorno en menos de 2 minutos.
    </p>

    <div class="bg-indigo-500/10 border border-indigo-500/30 rounded-xl p-5 mb-8 text-left">
        <h4 class="font-medium text-indigo-400 flex items-center gap-2 mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Antes de comenzar, asegúrate de tener:
        </h4>
        <ul class="list-disc list-inside text-sm text-slate-300 space-y-1 ml-1">
            <li>Tus credenciales de la Base de Datos (Host, Usuario, Contraseña, Nombre).</li>
            <li>Versión de PHP 8.2 o superior instalada.</li>
        </ul>
    </div>

    <a href="{{ route('install.requirements') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg px-6 py-3 transition-all duration-300 transform active:scale-95 shadow-lg shadow-indigo-600/30">
        Comenzar Instalación
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </a>
</div>
@endsection
