@extends('admin.layouts.app')
@section('title', 'Carga Masiva de Correos - Panel de Administración')
@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- BACK + HEADER (HERO STYLE) --}}
    <div class="ui-anim-in">
        <a href="{{ route('admin.allowed-emails.index') }}" wire:navigate class="ui-back-link">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver a Correos Autorizados
        </a>

        <div class="ui-hero">
            <div>
                <div class="ui-hero-tag">
                    Herramienta
                </div>
                <h1 class="ui-hero-title">Carga Masiva</h1>
                <p class="ui-hero-sub">Sube un archivo .txt con la lista de correos que deseas autorizar de forma masiva.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:0.75rem; padding:1rem; margin-bottom:1.5rem;">
            <ul style="color:#f87171; font-size:0.85rem; margin-left:1.5rem; list-style:disc;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.allowed-emails.mass-store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- BLOQUE 1: Opciones de Carga --}}
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        Opciones de Carga
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label for="platform_id" class="ui-label">Plataforma</label>
                        <select id="platform_id" name="platform_id" class="ui-select">
                            <option value="">Sin plataforma</option>
                            @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="email_account_id" class="ui-label">Servidor IMAP</label>
                        <select id="email_account_id" name="email_account_id" class="ui-select">
                            <option value="">Automático (busca en todos)</option>
                            @foreach($emailAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="emails_text" class="ui-label" style="display:block; margin-bottom:0.5rem; font-size:0.85rem; font-weight:700; color:white;">Pegar lista de correos</label>
                    <textarea id="emails_text" name="emails_text" rows="5" class="ui-input" style="width:100%; resize:vertical; padding:1rem; background:rgba(15,23,42,0.6); border:1px solid rgba(255,255,255,0.1); border-radius:0.75rem; color:white;" placeholder="ejemplo1@correo.com&#10;ejemplo2@correo.com&#10;Incluso si hay texto sucio alrededor, el sistema extraerá los correos."></textarea>
                </div>
                
                <div style="text-align:center; margin-bottom:1.5rem; color:rgba(255,255,255,0.4); font-size:0.9rem; font-weight:700;">
                    &mdash; O SUBIR ARCHIVO &mdash;
                </div>

                <div style="background:rgba(255,255,255,0.02); border:1px dashed rgba(255,255,255,0.1); border-radius:0.75rem; padding:3rem 2rem; text-align:center;">
                    <label for="file" style="cursor:pointer; display:block;">
                        <div style="width:4rem; height:4rem; margin:0 auto 1.5rem; border-radius:1rem; background:rgba(168,85,247,0.1); display:flex; align-items:center; justify-content:center;">
                            <svg style="color:#a855f7;" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div style="font-weight:800; font-size:1.1rem; color:white; margin-bottom:0.35rem;">Haz clic para seleccionar tu archivo</div>
                        <div style="font-size:0.85rem; color:rgba(148,163,184,0.6);">Sube un archivo .txt o .csv</div>
                    </label>
                    <input type="file" id="file" name="file" accept=".txt,.csv" style="display:none;" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado'">
                    <div id="file-name" style="margin-top:1.5rem; font-size:0.85rem; font-weight:700; color:#c4b5fd;">Ningún archivo seleccionado</div>
                </div>
            </div>
        </div>

        {{-- BOTONES --}}
        <div class="ui-anim-in ui-delay-2 ui-form-actions">
            <a href="{{ route('admin.allowed-emails.index') }}" wire:navigate class="ui-btn ui-btn-secondary ui-btn-large" style="flex:1;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> 
                Cancelar
            </a>
            <button type="submit" class="ui-btn ui-btn-primary ui-btn-large" style="flex:2;">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> 
                Procesar Archivo
            </button>
        </div>
    </form>
</div>
@endsection
