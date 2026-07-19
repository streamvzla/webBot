@extends('admin.layouts.app')

@section('title', 'Mi Perfil - Panel de Administración')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    {{-- ── HERO HEADER ── --}}
    <div class="ui-hero ui-anim-in">
        <div>
            <div style="font-size:0.68rem;font-weight:800;letter-spacing:0.15em;text-transform:uppercase;color:var(--ui-primary-1);margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Ajustes Personales
            </div>
            <h1 class="ui-hero-title">Mi Perfil</h1>
            <p class="ui-hero-sub">Actualiza tus credenciales de acceso y los enlaces de contacto que verán tus clientes.</p>
        </div>
    </div>

    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ── BLOQUE 1: Credenciales ── --}}
        <div class="ae-card ui-anim-in ui-delay-1">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap" style="color:#c084fc;background:rgba(192,132,252,0.1);border-color:rgba(192,132,252,0.2);">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                        Información Personal
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label for="name" class="ui-label">Nombre Completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="ui-input" placeholder="Tu nombre">
                        @error('name') <p class="ui-error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="ui-label">Correo Electrónico *</label>
                        <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}" class="ui-input" placeholder="tucorreo@ejemplo.com">
                        @error('email') <p class="ui-error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="ui-label">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" min="6" class="ui-input" placeholder="••••••••">
                    <p class="ui-hint-msg">Déjalo en blanco si no deseas cambiarla.</p>
                    @error('password') <p class="ui-error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ── BLOQUE 2: Enlaces de Contacto ── --}}
        <div class="ae-card ui-anim-in ui-delay-2">
            <div class="ae-card-head">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div class="ui-icon-wrap" style="color:#38bdf8;background:rgba(56,189,248,0.1);border-color:rgba(56,189,248,0.2);">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        Redes y Contacto Público
                    </div>
                </div>
            </div>

            <div class="ae-card-body">
                <p style="font-size:0.9rem; color:rgba(148,163,184,0.8); margin-bottom:1.5rem; max-width:40rem; line-height:1.5;">
                    Estos enlaces serán visibles para tus clientes dentro de su propio panel, facilitando que te contacten directamente por soporte.
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label for="whatsapp" class="ui-label" style="display:flex;align-items:center;gap:0.5rem;">
                            <svg class="w-4 h-4" style="color:#4ade80;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            Enlace de WhatsApp
                        </label>
                        <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" class="ui-input" placeholder="https://wa.me/...">
                        @error('whatsapp') <p class="ui-error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="telegram" class="ui-label" style="display:flex;align-items:center;gap:0.5rem;">
                            <svg class="w-4 h-4" style="color:#60a5fa;" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                            Enlace de Telegram
                        </label>
                        <input type="text" id="telegram" name="telegram" value="{{ old('telegram', $user->telegram) }}" class="ui-input" placeholder="https://t.me/...">
                        @error('telegram') <p class="ui-error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label for="website" class="ui-label" style="display:flex;align-items:center;gap:0.5rem;">
                            <svg class="w-4 h-4" style="color:#e879f9;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            Sitio Web o Instagram
                        </label>
                        <input type="url" id="website" name="website" value="{{ old('website', $user->website) }}" class="ui-input" placeholder="https://tu-sitio.com">
                        @error('website') <p class="ui-error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── BOTONES DE ACCIÓN ── --}}
        <div class="ui-anim-in ui-delay-3 ui-form-actions">
            <a href="{{ route('admin.dashboard') }}" class="ui-btn ui-btn-cancel">
                Cancelar
            </a>
            <button type="submit" class="ui-btn ui-btn-primary ui-btn-large">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Guardar Cambios
            </button>
        </div>

    </form>
</div>
@endsection
