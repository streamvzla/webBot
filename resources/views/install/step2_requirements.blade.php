@extends('install.layout')

@section('title', 'Requisitos')
@section('subtitle', 'Paso 2: Comprobación del Servidor')

@php $step = 2; @endphp

@section('content')
<div>
    <h2 class="text-xl font-semibold mb-6 text-white border-b border-white/10 pb-4">Requisitos del Sistema</h2>
    
    <div class="space-y-4 mb-8">
        <!-- PHP Version -->
        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 border {{ $requirements['php'] ? 'border-green-500/30' : 'border-red-500/30' }}">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-300">Versión PHP (8.3+)</span>
            </div>
            <div>
                @if($requirements['php'])
                    <span class="text-green-400 text-sm font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> {{ PHP_VERSION }}</span>
                @else
                    <div class="flex flex-col items-end">
                        <span class="text-red-400 text-sm font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> {{ PHP_VERSION }}</span>
                        <span class="text-[10px] text-red-400/80 mt-1 font-medium">Requiere actualizar a PHP 8.3+</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Extensions -->
        <div class="grid grid-cols-2 gap-3">
            @foreach(['pdo', 'mbstring', 'openssl', 'curl', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'imap'] as $ext)
            <div class="flex flex-col justify-center p-3 rounded-lg bg-slate-900/30 border {{ $requirements[$ext] ? 'border-green-500/20' : 'border-red-500/20' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-300">{{ strtoupper($ext) }}</span>
                    @if($requirements[$ext])
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @else
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    @endif
                </div>
                @if(!$requirements[$ext])
                    <span class="text-[10px] text-red-400/80 mt-1">Activar extensión en cPanel</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <h2 class="text-xl font-semibold mb-4 text-white border-b border-white/10 pb-4">Permisos de Carpetas</h2>
    <div class="space-y-3 mb-8">
        @foreach([
            'storage' => 'storage/',
            'bootstrap' => 'bootstrap/cache/',
            'env' => 'Directorio Raíz (.env)'
        ] as $key => $label)
        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 border {{ $permissions[$key] ? 'border-green-500/30' : 'border-red-500/30' }}">
            <span class="text-sm font-medium text-slate-300">{{ $label }}</span>
            @if($permissions[$key])
                <span class="text-green-400 text-xs font-bold px-2 py-1 bg-green-400/10 rounded">ESCRIBIBLE</span>
            @else
                <div class="flex flex-col items-end">
                    <span class="text-red-400 text-xs font-bold px-2 py-1 bg-red-400/10 rounded">SOLO LECTURA</span>
                    <span class="text-[10px] text-red-400/80 mt-1 font-medium">Asignar permisos CHMOD 775</span>
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="flex justify-between items-center mt-8">
        <a href="{{ route('install.step1') }}" class="text-slate-400 hover:text-white transition text-sm">Volver</a>
        
        @if($allRequirementsMet)
            <a href="{{ route('install.database') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg px-6 py-2.5 transition-all shadow-lg shadow-indigo-600/30">
                Continuar
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        @else
            <button disabled class="inline-flex items-center gap-2 bg-slate-700 text-slate-400 font-medium rounded-lg px-6 py-2.5 cursor-not-allowed">
                Corrige los errores
            </button>
        @endif
    </div>
</div>
@endsection


