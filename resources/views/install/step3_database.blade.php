@extends('install.layout')

@section('title', 'Base de Datos')
@section('subtitle', 'Paso 3: Conexión de Base de Datos')

@php $step = 3; @endphp

@section('content')
<div>
    <h2 class="text-xl font-semibold mb-6 text-white border-b border-white/10 pb-4">Configurar Base de Datos</h2>
    
    <form action="{{ route('install.database.post') }}" method="POST" class="space-y-5">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Host -->
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300">Database Host</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <!-- Port -->
            <div class="space-y-1">
                <label class="block text-sm font-medium text-slate-300">Database Port</label>
                <input type="text" name="db_port" value="{{ old('db_port', '3306') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Database Name -->
        <div class="space-y-1">
            <label class="block text-sm font-medium text-slate-300">Nombre de la Base de Datos</label>
            <input type="text" name="db_database" value="{{ old('db_database', 'nexuscode') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <!-- Username -->
        <div class="space-y-1">
            <label class="block text-sm font-medium text-slate-300">Usuario (Username)</label>
            <input type="text" name="db_username" value="{{ old('db_username', 'root') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <label class="block text-sm font-medium text-slate-300">Contraseña (Password)</label>
            <input type="password" name="db_password" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Dejar en blanco si no tiene">
        </div>

        <div class="flex justify-between items-center pt-6 mt-6 border-t border-white/10">
            <a href="{{ route('install.requirements') }}" class="text-slate-400 hover:text-white transition text-sm">Volver</a>
            <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg px-6 py-2.5 transition-all shadow-lg shadow-indigo-600/30">
                Testear y Continuar
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </form>
</div>
@endsection

