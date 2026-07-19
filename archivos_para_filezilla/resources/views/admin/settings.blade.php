@extends('admin.layouts.app')

@section('title', 'Ajustes del Sistema - Panel de Administración')
@section('header', 'Configuración General')
@section('description', 'Control maestro de la plataforma, marcas y restricciones')

@section('content')

<div class="max-w-7xl mx-auto space-y-6 pb-24">
    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in">
        <div>
            <div class="ui-hero-tag">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Configuración General
            </div>
            <h1 class="ui-hero-title">Ajustes del Sistema</h1>
            <p class="ui-hero-sub">Control maestro de la plataforma, marcas y restricciones</p>
        </div>
    </div>

    <!-- Tabs Header -->
    <div class="ae-toolbar ui-anim-in ui-delay-1" style="display:flex; flex-wrap:wrap; gap:0.5rem; justify-content:flex-start; margin-bottom:1.5rem;">
        @if(auth()->id() === 1)
        <button onclick="switchTab('general')" id="tab-btn-general" class="tab-btn active" style="padding:0.6rem 1.2rem; border-radius:0.75rem; font-size:0.85rem; font-weight:700; cursor:pointer; transition:all 0.2s; border:1px solid rgba(168,85,247,0.3); background:rgba(168,85,247,0.15); color:#c084fc;">General</button>
        <button onclick="switchTab('appearance')" id="tab-btn-appearance" class="tab-btn" style="padding:0.6rem 1.2rem; border-radius:0.75rem; font-size:0.85rem; font-weight:600; cursor:pointer; transition:all 0.2s; border:1px solid transparent; background:transparent; color:rgba(148,163,184,0.7);">Apariencia (Marca Blanca)</button>
        @endif
        <button onclick="switchTab('contact')" id="tab-btn-contact" class="tab-btn" style="padding:0.6rem 1.2rem; border-radius:0.75rem; font-size:0.85rem; font-weight:{{ auth()->id() !== 1 ? '700' : '600' }}; cursor:pointer; transition:all 0.2s; border:1px solid {{ auth()->id() !== 1 ? 'rgba(168,85,247,0.3)' : 'transparent' }}; background:{{ auth()->id() !== 1 ? 'rgba(168,85,247,0.15)' : 'transparent' }}; color:{{ auth()->id() !== 1 ? '#c084fc' : 'rgba(148,163,184,0.7)' }};">Contacto y Soporte</button>
        <button onclick="switchTab('security')" id="tab-btn-security" class="tab-btn" style="padding:0.6rem 1.2rem; border-radius:0.75rem; font-size:0.85rem; font-weight:600; cursor:pointer; transition:all 0.2s; border:1px solid transparent; background:transparent; color:rgba(148,163,184,0.7);">Seguridad y API</button>
        <button onclick="switchTab('docs')" id="tab-btn-docs" class="tab-btn" style="padding:0.6rem 1.2rem; border-radius:0.75rem; font-size:0.85rem; font-weight:600; cursor:pointer; transition:all 0.2s; border:1px solid transparent; background:transparent; color:rgba(148,163,184,0.7);">Documentación API</button>
    </div>

    <!-- Main Form for Settings (General, Appearance, Contact) -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- TAB: GENERAL -->
        @if(auth()->id() === 1)
        <div id="tab-content-general" class="tab-content block animate-fade-in-up">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Identidad (Solo Nombre Comercial) -->
                <div class="ae-card ui-anim-in ui-delay-1">
                    <div class="ae-card-head">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#c084fc; background:rgba(192,132,252,0.15);">
                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                </div>
                                Identidad Básica
                            </div>
                        </div>
                    </div>
                    <div class="ae-card-body">
                        <div>
                            <label for="site_name" class="ui-label">Nombre Comercial</label>
                            <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="ui-input text-lg font-medium" placeholder="Ej. Mi Empresa">
                        </div>
                    </div>
                </div>

                <!-- Búsqueda y SEO -->
                <div class="ae-card ui-anim-in ui-delay-2">
                    <div class="ae-card-head">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#10b981; background:rgba(16,185,129,0.15);">
                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                </div>
                                Búsqueda y SEO
                            </div>
                        </div>
                    </div>
                    <div class="ae-card-body">
                        <div style="display:grid; gap:1.5rem;">
                            <div>
                                <label for="seo_title" class="ui-label">Título de la Web</label>
                                <input type="text" id="seo_title" name="seo_title" value="{{ old('seo_title', $settings['seo_title']) }}" class="ui-input" placeholder="Ej. Panel de Códigos">
                            </div>
                            <div>
                                <label for="vendor_id" class="ui-label">ID Vendedor (Interno)</label>
                                <input type="text" id="vendor_id" name="vendor_id" value="{{ old('vendor_id', $settings['vendor_id']) }}" class="ui-input font-mono" placeholder="Ej. VEND-001">
                            </div>
                            <div>
                                <label for="seo_description" class="ui-label">Descripción (Meta Description)</label>
                                <textarea id="seo_description" name="seo_description" rows="2" class="ui-input resize-none" placeholder="Breve descripción...">{{ old('seo_description', $settings['seo_description']) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restricciones -->
                <div class="ae-card lg:col-span-2 ui-anim-in ui-delay-3" style="border-top:2px solid rgba(239,68,68,0.5);">
                    <div class="ae-card-head">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#ef4444; background:rgba(239,68,68,0.15);">
                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                Restricciones de Uso
                            </div>
                        </div>
                    </div>
                    <div class="ae-card-body">
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                            <div style="padding:1.5rem; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:1rem; cursor:pointer;" onclick="document.getElementById('email_filter_toggle').click()">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p style="color:white; font-weight:800; font-size:1rem;">Whitelist Estricta</p>
                                        <p style="color:rgba(148,163,184,0.7); font-size:0.8rem; margin-top:0.25rem;">Los clientes SOLO podrán consultar correos registrados en 'Correos Autorizados'. Bloquea cualquier otro intento.</p>
                                    </div>
                                    <label class="ui-toggle-wrap">
                                        <input type="hidden" name="email_filter_enabled" value="0">
                                        <input type="checkbox" id="email_filter_toggle" name="email_filter_enabled" value="1" {{ $settings['email_filter_enabled'] ? 'checked' : '' }} class="ui-toggle-inp">
                                        <div class="ui-toggle-track"><div class="ui-toggle-thumb"></div></div>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label for="query_cooldown_minutes" class="ui-label">Enfriamiento (Anti-Spam)</label>
                                <div style="position:relative;">
                                    <input type="number" id="query_cooldown_minutes" name="query_cooldown_minutes" value="{{ old('query_cooldown_minutes', $settings['query_cooldown_minutes']) }}" min="1" max="1440" class="ui-input font-mono text-lg" style="color:#f87171; padding-right:5rem;">
                                    <div style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); font-weight:800; font-size:0.8rem; color:rgba(148,163,184,0.5);">
                                        MINUTOS
                                    </div>
                                </div>
                                <p style="color:rgba(148,163,184,0.6); font-size:0.75rem; margin-top:0.5rem;">Tiempo que un cliente debe esperar antes de volver a solicitar un código.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- TAB: APPEARANCE -->
        @if(auth()->id() === 1)
        <div id="tab-content-appearance" class="tab-content hidden animate-fade-in-up">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Logo -->
                <div class="ae-card ui-anim-in ui-delay-1">
                    <div class="ae-card-head">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#ec4899; background:rgba(236,72,153,0.15);">
                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                Logotipo
                            </div>
                        </div>
                    </div>
                    <div class="ae-card-body">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-4 rounded-xl border border-dashed border-slate-600 bg-slate-900/30 hover:bg-slate-800 transition">
                            @if($settings['site_logo'])
                                <div class="relative group w-24 h-24 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="max-w-full max-h-full object-contain p-2">
                                </div>
                            @else
                                <div class="w-24 h-24 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0 border-dashed">
                                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            <div class="flex-1 w-full">
                                <input type="file" id="site_logo" name="site_logo" accept="image/*" class="ui-input w-full p-2" style="font-size:0.8rem; height:auto;">
                                <p class="text-xs text-slate-500 mt-2">Recomendado: PNG transparente (500x500px). Máx 2MB.</p>
                                
                                @if($settings['site_logo'])
                                <label class="flex items-center gap-2 mt-3 cursor-pointer group w-max">
                                    <input type="checkbox" name="delete_logo" value="1" class="w-4 h-4 rounded border-slate-600 bg-slate-900 text-red-500 focus:ring-red-500 focus:ring-offset-slate-900">
                                    <span class="text-sm text-red-400 group-hover:text-red-300 transition">Eliminar logotipo actual</span>
                                </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colores Dinámicos -->
                <div class="ae-card ui-anim-in ui-delay-2">
                    <div class="ae-card-head">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#eab308; background:rgba(234,179,8,0.15);">
                                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                </div>
                                Paleta de Colores
                            </div>
                        </div>
                    </div>
                    
                    <div class="ae-card-body space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="ui-label">Color Primario</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="theme_color_primary" value="{{ old('theme_color_primary', $settings['theme_color_primary']) }}" class="w-10 h-10 rounded cursor-pointer bg-transparent border-0 p-0">
                                    <span class="text-sm font-mono text-slate-300">{{ old('theme_color_primary', $settings['theme_color_primary']) }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="ui-label">Color Secundario</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="theme_color_secondary" value="{{ old('theme_color_secondary', $settings['theme_color_secondary']) }}" class="w-10 h-10 rounded cursor-pointer bg-transparent border-0 p-0">
                                    <span class="text-sm font-mono text-slate-300">{{ old('theme_color_secondary', $settings['theme_color_secondary']) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-700/50">
                            <div>
                                <label class="ui-label">Fondo (Inicio)</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="theme_bg_start" value="{{ old('theme_bg_start', $settings['theme_bg_start']) }}" class="w-10 h-10 rounded cursor-pointer bg-transparent border-0 p-0">
                                    <span class="text-sm font-mono text-slate-300">{{ old('theme_bg_start', $settings['theme_bg_start']) }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="ui-label">Fondo (Fin)</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="theme_bg_end" value="{{ old('theme_bg_end', $settings['theme_bg_end']) }}" class="w-10 h-10 rounded cursor-pointer bg-transparent border-0 p-0">
                                    <span class="text-sm font-mono text-slate-300">{{ old('theme_bg_end', $settings['theme_bg_end']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- TAB: CONTACTO -->
        <div id="tab-content-contact" class="tab-content {{ auth()->id() !== 1 ? 'block' : 'hidden' }} animate-fade-in-up">
            <div class="ae-card max-w-3xl mx-auto ui-anim-in ui-delay-1">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap" style="color:#3b82f6; background:rgba(59,130,246,0.15);">
                                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                            </div>
                            Soporte y Contacto
                        </div>
                    </div>
                </div>
                
                <div class="ae-card-body space-y-6">
                    <div>
                        <label class="ui-label">Web / Tienda Personal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            </div>
                            <input type="url" name="website" value="{{ old('website', $user->website) }}" class="ui-input pl-9 text-sm" placeholder="https://miweb.com">
                        </div>
                    </div>
                    
                    <div>
                        <label class="ui-label">Grupo/Canal Telegram Personal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.18-.08-.05-.19-.02-.27 0-.11.03-1.84 1.18-5.21 3.45-.49.33-.94.5-1.35.49-.45-.01-1.3-.25-1.94-.46-.78-.26-1.4-.4-1.35-.85.03-.23.34-.47.93-.72 3.66-1.59 6.09-2.64 7.32-3.15 3.48-1.45 4.21-1.7 4.69-1.71.11 0 .34.03.47.14.11.09.15.22.15.35-.01.07-.01.16-.02.21z"/></svg>
                            </div>
                            <input type="url" name="telegram" value="{{ old('telegram', $user->telegram) }}" class="ui-input pl-9 text-sm" placeholder="https://t.me/...">
                        </div>
                    </div>
                    
                    <div>
                        <label class="ui-label">WhatsApp Personal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-bold">+</div>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" class="ui-input pl-8 text-sm" placeholder="Ej. 1234567890 (Código País + Número sin signos)">
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Tus clientes de tu franquicia verán tus enlaces en su panel lateral y menú.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón de Guardar -->
        <div id="save-bar-wrapper">
            <div class="ui-form-actions ui-anim-in ui-delay-3" style="justify-content:flex-end;">
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-large" style="padding:0 3rem;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Guardar Configuración
                </button>
            </div>
        </div>
    </form>

    <!-- TAB: SECURITY & API -->
    <div id="tab-content-security" class="tab-content hidden animate-fade-in-up">
        <!-- 2FA -->
        <div class="ae-card mb-6 ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap" style="color:#6366f1; background:rgba(99,102,241,0.15);">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                        </div>
                        Autenticación 2FA
                    </div>
                </div>
                <div class="ae-card-actions">
                    @if(auth()->user()->two_factor_confirmed_at)
                        <div class="ui-badge-neon success">Activado</div>
                    @else
                        <div class="ui-badge-neon" style="color:rgba(148,163,184,0.7); background:rgba(255,255,255,0.05); border-color:rgba(255,255,255,0.1);">Desactivado</div>
                    @endif
                </div>
            </div>

            <div class="ae-card-body space-y-4">
                @if(!auth()->user()->two_factor_confirmed_at)
                    <p class="text-slate-300">
                        La autenticación de dos factores agrega una capa adicional de seguridad a tu cuenta. Cuando esté habilitada, se te pedirá un token numérico aleatorio seguro durante la autenticación.
                    </p>
                    <button type="button" onclick="enable2FA()" class="ui-btn ui-btn-primary" style="width:auto; padding:0 2rem;">
                        Configurar 2FA
                    </button>
                    
                    <div id="2fa-setup-box" class="hidden mt-6 p-6 bg-slate-900 border border-slate-700 rounded-xl">
                        <h4 class="font-bold text-white mb-2">Paso 1: Escanea el Código QR</h4>
                        <p class="text-sm text-slate-400 mb-4">Abre la aplicación Google Authenticator y escanea el siguiente código QR:</p>
                        <div class="bg-white p-2 rounded-lg inline-block mb-4" id="qr-code-container">
                        </div>
                        <p class="text-xs text-slate-500 mb-6">Si no puedes escanearlo, usa la clave secreta: <span id="secret-key" class="font-mono text-indigo-400 font-bold ml-2"></span></p>

                        <h4 class="font-bold text-white mb-2">Paso 2: Confirma el código</h4>
                        <form id="form-confirm-2fa" class="flex gap-4">
                            <input type="text" id="2fa_code" required class="ui-input font-mono tracking-widest text-center text-xl w-40" placeholder="••••••" maxlength="6" pattern="[0-9]*">
                            <button type="submit" class="ui-btn ui-btn-primary">Confirmar</button>
                        </form>
                    </div>
                @else
                    <p class="text-slate-300">Has habilitado la autenticación de dos factores. Ahora tu cuenta es mucho más segura.</p>
                    <form action="{{ route('admin.2fa.disable') }}" method="POST" id="form-disable-2fa">
                        @csrf
                        <button type="submit" class="ui-btn ui-btn-cancel">Deshabilitar 2FA</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- API Keys -->
        <div class="ae-card ui-anim-in ui-delay-2">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap" style="color:#06b6d4; background:rgba(6,182,212,0.15);">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </div>
                        Gestor de API Keys
                    </div>
                </div>
            </div>

            <div class="ae-card-body space-y-6">
                <p class="text-slate-300 text-sm">
                    Las llaves de API (Tokens) permiten que aplicaciones externas se conecten al sistema.
                </p>

                @if(session('success') && str_contains(session('success'), 'API Key generada'))
                    <div style="padding:1rem; border:1px solid rgba(16,185,129,0.3); background:rgba(16,185,129,0.1); border-radius:1rem;">
                        <p class="text-emerald-400 font-bold mb-2">¡Token generado con éxito!</p>
                        <p class="text-white text-sm break-all font-mono bg-slate-900 p-3 rounded-lg border border-slate-700">{{ str_replace('API Key generada exitosamente. Guárdala ahora, no se volverá a mostrar: ', '', session('success')) }}</p>
                        <p class="text-red-400 text-xs mt-2 font-semibold">Copia este token ahora. Por seguridad, no podrás volver a verlo.</p>
                    </div>
                @endif

                <form action="{{ route('admin.api-keys.generate') }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                    @csrf
                    <input type="text" name="token_name" required placeholder="Ej. Bot WhatsApp" class="ui-input flex-1">
                    <button type="submit" class="ui-btn ui-btn-primary">Generar Nueva Llave</button>
                </form>

                <div class="mt-8">
                    <h4 class="ui-label">Llaves Activas</h4>
                    @if(auth()->user()->tokens->count() > 0)
                        <div class="space-y-3">
                            @foreach(auth()->user()->tokens as $token)
                                <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:1rem;">
                                    <div>
                                        <p style="color:white; font-weight:700;">{{ $token->name }}</p>
                                        <p style="color:rgba(148,163,184,0.6); font-size:0.75rem;">Creada: {{ $token->created_at->format('d/m/Y H:i') }} • Último uso: {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca' }}</p>
                                    </div>
                                    <form action="{{ route('admin.api-keys.revoke', $token->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ui-btn ui-btn-cancel" style="padding:0.5rem; border-color:rgba(239,68,68,0.3); color:#ef4444;" title="Revocar Llave">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding:2rem; text-align:center; border:1px dashed rgba(255,255,255,0.1); border-radius:1rem;">
                            <p style="color:rgba(148,163,184,0.6); font-size:0.85rem;">No tienes llaves API activas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- TAB: API DOCS -->
    <div id="tab-content-docs" class="tab-content hidden animate-fade-in-up">
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap" style="color:#8b5cf6; background:rgba(139,92,246,0.15);">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </div>
                        Documentación de la API Pública
                    </div>
                </div>
            </div>
            
            <div class="ae-card-body space-y-8">
                <p class="text-slate-300">Utiliza tu <span class="font-mono text-cyan-400 bg-slate-900 px-2 py-1 rounded">API Key</span> como Bearer Token en la cabecera <code>Authorization</code> para todas las peticiones.</p>

                <!-- Endpoint 1 -->
                <div style="border:1px solid rgba(255,255,255,0.05); border-radius:1rem; overflow:hidden;">
                    <div style="padding:1rem; background:rgba(255,255,255,0.02); border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:1rem;">
                        <span style="background:#10b981; color:white; font-weight:800; padding:0.25rem 0.75rem; border-radius:0.5rem; font-size:0.75rem;">GET</span>
                        <code style="color:white; font-family:monospace; font-size:1.1rem;">/api/v1/emails/recent</code>
                    </div>
                    <div style="padding:1.5rem;">
                        <p style="color:rgba(148,163,184,0.8); margin-bottom:1rem; font-size:0.9rem;">Devuelve la lista de los correos autorizados disponibles más recientes.</p>
                        <h5 style="color:rgba(148,163,184,0.5); font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Respuesta Exitosa (200 OK)</h5>
                        <pre style="background:rgba(0,0,0,0.4); padding:1rem; border-radius:0.75rem; color:#34d399; font-family:monospace; font-size:0.85rem; overflow-x:auto;">
{
  "success": true,
  "data": [
    {
      "email": "ejemplo@netflix.com",
      "created_at": "2026-07-06T12:00:00Z"
    }
  ]
}</pre>
                    </div>
                </div>

                <!-- Endpoint 2 -->
                <div style="border:1px solid rgba(255,255,255,0.05); border-radius:1rem; overflow:hidden;">
                    <div style="padding:1rem; background:rgba(255,255,255,0.02); border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:1rem;">
                        <span style="background:#3b82f6; color:white; font-weight:800; padding:0.25rem 0.75rem; border-radius:0.5rem; font-size:0.75rem;">POST</span>
                        <code style="color:white; font-family:monospace; font-size:1.1rem;">/api/v1/query</code>
                    </div>
                    <div style="padding:1.5rem;">
                        <p style="color:rgba(148,163,184,0.8); margin-bottom:1rem; font-size:0.9rem;">Ejecuta una consulta al servidor para buscar códigos en un correo específico.</p>
                        
                        <h5 style="color:rgba(148,163,184,0.5); font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Cuerpo de la Petición (JSON)</h5>
                        <pre style="background:rgba(0,0,0,0.4); padding:1rem; border-radius:0.75rem; color:#93c5fd; font-family:monospace; font-size:0.85rem; overflow-x:auto; margin-bottom:1.5rem;">
{
  "email": "ejemplo@netflix.com"
}</pre>

                        <h5 style="color:rgba(148,163,184,0.5); font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Respuesta Exitosa (200 OK)</h5>
                        <pre style="background:rgba(0,0,0,0.4); padding:1rem; border-radius:0.75rem; color:#34d399; font-family:monospace; font-size:0.85rem; overflow-x:auto;">
{
  "success": true,
  "message": "Consulta procesada correctamente vía API",
  "data": {
    "email": "ejemplo@netflix.com",
    "codes": [
      {
        "code": "G-123456",
        "platform": "Google"
      }
    ],
    "query_id": 124
  }
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sistema de Pestañas (Vanilla JS)
    function switchTab(tabId) {
        // Ocultar todos los contenidos
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('block');
        });
        
        // Quitar estado activo de todos los botones
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.style.background='transparent'; 
            el.style.color='rgba(148,163,184,0.7)'; 
            el.style.borderColor='transparent'; 
            el.style.fontWeight='600';
            el.classList.remove('active');
        });
        
        // Mostrar contenido seleccionado
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');
        document.getElementById('tab-content-' + tabId).classList.add('block');
        
        // Marcar botón activo
        const btn = document.getElementById('tab-btn-' + tabId);
        btn.classList.add('active');
        btn.style.background='rgba(168,85,247,0.15)'; 
        btn.style.color='#c084fc'; 
        btn.style.borderColor='rgba(168,85,247,0.3)'; 
        btn.style.fontWeight='700';

        // Mostrar/Ocultar barra de guardar (Security & API no la necesitan)
        const saveBar = document.getElementById('save-bar-wrapper');
        if (tabId === 'security' || tabId === 'docs') {
            saveBar.style.display = 'none';
        } else {
            saveBar.style.display = 'block';
        }
    }

    // Inicializar mostrando general si hay hash en url, u otra lógica
    window.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.replace('#', '');
        const isSuperAdmin = {{ auth()->id() === 1 ? 'true' : 'false' }};
        if (['general', 'appearance', 'contact', 'security', 'docs'].includes(hash)) {
            switchTab(hash);
        } else if (!isSuperAdmin) {
            switchTab('contact');
        }
    });

    // 2FA Functions
    function enable2FA() {
        fetch('{{ route("admin.2fa.enable") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('qr-code-container').innerHTML = data.qrCode;
                document.getElementById('secret-key').innerText = data.secret;
                document.getElementById('2fa-setup-box').classList.remove('hidden');
            }
        });
    }

    document.getElementById('form-confirm-2fa')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('2fa_code').value;
        fetch('{{ route("admin.2fa.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Toast.fire({ icon: 'success', title: '2FA activado correctamente' });
                setTimeout(() => {
                    window.location.hash = 'security';
                    window.location.reload();
                }, 1500);
            } else {
                Toast.fire({ icon: 'error', title: data.message || 'Código incorrecto' });
            }
        });
    });

    document.getElementById('form-disable-2fa')?.addEventListener('submit', function(e) {
        e.preventDefault();
        if(confirm('¿Estás seguro de que deseas deshabilitar la autenticación de dos factores? Tu cuenta será menos segura.')) {
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Toast.fire({ icon: 'success', title: data.message });
                    setTimeout(() => {
                        window.location.hash = 'security';
                        window.location.reload();
                    }, 1500);
                }
            });
        }
    });
</script>
@endpush

@endsection
