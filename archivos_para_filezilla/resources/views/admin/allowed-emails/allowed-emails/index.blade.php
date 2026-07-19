@extends('admin.layouts.app')

@section('title', 'Correos Autorizados - Panel de Administración')
@section('header', 'Correos Autorizados')
@section('description', 'Gestiona los correos permitidos para consultas')

@section('content')

    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
        <div>
            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#7c3aed;margin-bottom:0.5rem;">
                Gestión de Inventario
            </div>
            <h1 class="ui-hero-title">Correos Autorizados</h1>
            <p class="ui-hero-sub">Administra los correos habilitados para recibir y consultar códigos en el sistema.</p>
        </div>
        <div style="display:flex;gap:0.75rem;flex-shrink:0;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('admin.allowed-emails.mass-upload') }}" class="ui-btn ui-btn-secondary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Carga Masiva
            </a>
        </div>
    </div>

    {{-- ── COMPONENTE LIVEWIRE (contiene stats + filtros + tabla) ── --}}
    <livewire:admin.allowed-email-list />

@endsection
