@extends('admin.layouts.app')

@section('title', 'Plataformas - Panel de Administración')
@section('header', 'Gestión de Plataformas')
@section('description', 'Administra los servicios de streaming y sus configuraciones')

@section('content')

    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in" style="margin-bottom:2rem;">
        <div>
            <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#7c3aed;margin-bottom:0.5rem;">
                Administración Global
            </div>
            <h1 class="ui-hero-title">Gestión de Plataformas</h1>
            <p class="ui-hero-sub">Administra los servicios de streaming y sus configuraciones a nivel global.</p>
        </div>
    </div>

    {{-- ── COMPONENTE LIVEWIRE ── --}}
    @livewire('admin.platform-list')

@endsection
