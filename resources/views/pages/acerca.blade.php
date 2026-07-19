@extends('public.layouts.app')

@section('title', 'Acerca del Sistema')

@section('styles')
<style>
    .pub-hero { text-align: center; margin-bottom: 2rem; padding: 1rem 0 0; }
    .pub-hero h1 {
        font-size: clamp(1.75rem, 5vw, 2.5rem); font-weight: 900;
        background: linear-gradient(135deg, #f1f5f9 0%, #a855f7 55%, #ec4899 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        line-height: 1.15; margin-bottom: 0.75rem;
    }
    .pub-hero p {
        font-size: 0.95rem; color: rgba(148,163,184,0.7); max-width: 440px;
        margin: 0 auto; line-height: 1.6;
    }
    
    .tech-list {
        display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;
    }
    .tech-badge {
        background: rgba(168,85,247,0.1); border: 1px solid rgba(168,85,247,0.25);
        border-radius: 1rem; padding: 0.25rem 0.75rem;
        font-size: 0.75rem; font-weight: 600; color: #c4b5fd;
    }
</style>
@endsection

@section('content')
<div class="pub-hero">
    <h1>Acerca del Sistema</h1>
    <p>La solución definitiva para la extracción y gestión automatizada de códigos de verificación.</p>
</div>

<div class="pcard" style="margin-bottom: 2rem;">
    <div class="pcard-body">
        
        <!-- Desarrollo Original -->
        <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1.5rem;">
            <div style="width: 3rem; height: 3rem; border-radius: 50%; background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(236,72,153,0.2)); border: 1px solid rgba(168,85,247,0.35); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" fill="none" stroke="#a855f7" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: #f1f5f9; margin-bottom: 0.25rem;">Desarrollo Original</h3>
                <p style="font-size: 0.85rem; color: rgba(148,163,184,0.7); margin-bottom: 0.5rem;">
                    Luis Martinez, desde Valencia-Venezuela
                    <svg width="16" height="11" viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:-2px;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,0.3);margin-left:4px;">
                        <rect width="36" height="8" fill="#FFCC00"/>
                        <rect y="8" width="36" height="8" fill="#00247D"/>
                        <rect y="16" width="36" height="8" fill="#CF142B"/>
                        <g fill="#fff">
                            <circle cx="11.5" cy="13.5" r="0.8"/>
                            <circle cx="13" cy="11.5" r="0.8"/>
                            <circle cx="15" cy="10" r="0.8"/>
                            <circle cx="17" cy="9.2" r="0.8"/>
                            <circle cx="19" cy="9.2" r="0.8"/>
                            <circle cx="21" cy="10" r="0.8"/>
                            <circle cx="23" cy="11.5" r="0.8"/>
                            <circle cx="24.5" cy="13.5" r="0.8"/>
                        </g>
                    </svg>
                </p>
                <a href="https://tu-codigo.com" target="_blank" style="font-size: 0.85rem; color: #a855f7; text-decoration: none; font-weight: 600;">Visitar tu-codigo.com &rarr;</a>
            </div>
        </div>

        <!-- Tecnologías -->
        <div style="display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 3rem; height: 3rem; border-radius: 50%; background: linear-gradient(135deg, rgba(56,189,248,0.2), rgba(59,130,246,0.2)); border: 1px solid rgba(56,189,248,0.35); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" fill="none" stroke="#38bdf8" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: #f1f5f9; margin-bottom: 0.25rem;">Stack Tecnológico</h3>
                <div class="tech-list">
                    <span class="tech-badge">Laravel 11</span>
                    <span class="tech-badge">PHP 8.2+</span>
                    <span class="tech-badge">SQLite/MySQL</span>
                    <span class="tech-badge">TailwindCSS</span>
                    <span class="tech-badge">IMAP Protocol</span>
                </div>
            </div>
        </div>

    </div>
</div>

<div style="text-align: center;">
    <a href="{{ url('/') }}" style="color: rgba(148,163,184,0.7); text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: color 0.2s;">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver al Inicio
    </a>
</div>
@endsection
