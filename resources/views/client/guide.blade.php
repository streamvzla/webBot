@extends('client.layouts.app')

@section('title', 'Guía de Uso')

@section('styles')
<style>
    .guide-hero {
        background: linear-gradient(135deg, rgba(15,10,40,0.95) 0%, rgba(20,10,35,0.98) 100%);
        border: 1px solid rgba(168,85,247,0.2);
        border-radius: 1.5rem;
        padding: 2.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    .guide-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #a855f7, #ec4899, transparent);
    }
    .guide-hero::after {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(168,85,247,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .step-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(168,85,247,0.12);
        border-radius: 1.25rem;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .step-card:hover {
        border-color: rgba(168,85,247,0.35);
        background: rgba(168,85,247,0.04);
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(168,85,247,0.12);
    }
    .step-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(168,85,247,0.04) 0%, rgba(236,72,153,0.02) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .step-card:hover::before { opacity: 1; }

    .step-number {
        width: 3rem; height: 3rem;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; font-weight: 900;
        flex-shrink: 0;
        position: relative;
    }

    .tip-card {
        background: rgba(52,211,153,0.05);
        border: 1px solid rgba(52,211,153,0.15);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
    }

    .warn-card {
        background: rgba(239,68,68,0.05);
        border: 1px solid rgba(239,68,68,0.15);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(168,85,247,0.3), transparent);
        -webkit-text-fill-color: initial;
        background-clip: initial;
    }

    .faq-item {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1rem;
        overflow: hidden;
        transition: border-color 0.2s;
    }
    .faq-item:hover { border-color: rgba(168,85,247,0.25); }
    .faq-q {
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: rgba(226,232,240,0.9);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .faq-a {
        padding: 0 1.25rem 1rem;
        font-size: 0.85rem;
        color: rgba(148,163,184,0.8);
        line-height: 1.6;
        border-top: 1px solid rgba(255,255,255,0.05);
        display: none;
    }
    .faq-a.open { display: block; }
    .faq-icon { transition: transform 0.2s; flex-shrink: 0; }
    .faq-icon.open { transform: rotate(45deg); }
</style>
@endsection

@section('content')
<div style="max-width:860px;margin:0 auto;">

    {{-- HERO --}}
    <div class="guide-hero">
        <div style="width:5rem;height:5rem;background:linear-gradient(135deg,rgba(124,58,237,0.3),rgba(236,72,153,0.2));border:1.5px solid rgba(168,85,247,0.35);border-radius:1.5rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 0 40px rgba(168,85,247,0.2);">
            <svg width="36" height="36" fill="none" stroke="url(#gHero)" viewBox="0 0 24 24">
                <defs><linearGradient id="gHero" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#a855f7"/><stop offset="100%" stop-color="#ec4899"/></linearGradient></defs>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h1 style="font-size:1.875rem;font-weight:900;background:linear-gradient(135deg,#e2e8f0,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:0.5rem;">Guía de Uso</h1>
        <p style="color:rgba(148,163,184,0.7);font-size:0.95rem;max-width:480px;margin:0 auto;">Aprende a usar la plataforma de manera rápida y eficiente para obtener tus códigos de verificación al instante.</p>
    </div>

    {{-- STEPS --}}
    <div style="margin-bottom:2rem;">
        <div class="section-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Pasos para Consultar un Código
        </div>

        <div style="display:grid;gap:1rem;">
            @php
            $steps = [
                ['num'=>'1','color'=>'linear-gradient(135deg,#7c3aed,#a855f7)','shadow'=>'rgba(124,58,237,0.4)','icon'=>'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10','title'=>'Selecciona la Plataforma','desc'=>'En la página "Consultar Código", verás las plataformas disponibles (Netflix, Disney, etc.). Dale clic a la que necesitas y se resaltará en violeta indicando que está seleccionada.'],
                ['num'=>'2','color'=>'linear-gradient(135deg,#ec4899,#f43f5e)','shadow'=>'rgba(236,72,153,0.4)','icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z','title'=>'Presiona "Buscar Código"','desc'=>'Una vez seleccionada la plataforma, el botón se activará. Presiona "Buscar Código" y el sistema comenzará a buscar automáticamente el correo de verificación más reciente en los servidores.'],
                ['num'=>'3','color'=>'linear-gradient(135deg,#059669,#34d399)','shadow'=>'rgba(52,211,153,0.4)','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Recibe tu Código','desc'=>'En segundos verás el código o enlace de verificación en pantalla. Tendrás un temporizador de cuenta atrás, cópialo antes de que expire. Si es un enlace, podrás abrirlo directamente desde aquí.'],
                ['num'=>'4','color'=>'linear-gradient(135deg,#f59e0b,#fbbf24)','shadow'=>'rgba(245,158,11,0.4)','icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z','title'=>'Úsalo con Responsabilidad','desc'=>'Tienes un límite de consultas por día. No hagas clics rápidos repetidos ya que el sistema detecta actividad sospechosa. Si tu cuenta fue bloqueada, contacta al administrador.'],
            ];
            @endphp

            @foreach($steps as $step)
            <div class="step-card">
                <div style="display:flex;align-items:flex-start;gap:1.25rem;">
                    <div class="step-number" style="background:{{ $step['color'] }};box-shadow:0 4px 15px {{ $step['shadow'] }};">
                        <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $step['icon'] }}"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                            <span style="font-size:0.65rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:rgba(148,163,184,0.5);">Paso {{ $step['num'] }}</span>
                        </div>
                        <h3 style="font-size:1rem;font-weight:800;color:white;margin-bottom:0.375rem;">{{ $step['title'] }}</h3>
                        <p style="font-size:0.875rem;color:rgba(148,163,184,0.75);line-height:1.6;">{{ $step['desc'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- GARANTÍAS --}}
    <div style="margin-bottom:2rem;">
        <div class="section-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            ¿Cómo Funciona la Garantía?
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:1.25rem;">
            <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.18);border-radius:1rem;padding:1.5rem;">
                <div style="width:2.5rem;height:2.5rem;background:rgba(239,68,68,0.15);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;margin-bottom:0.875rem;">
                    <svg width="18" height="18" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h4 style="font-size:0.95rem;font-weight:800;color:#f87171;margin-bottom:0.375rem;">Reemplazo</h4>
                <p style="font-size:0.82rem;color:rgba(148,163,184,0.7);line-height:1.6;">Si tu cuenta cayó completamente y necesita ser cambiada por una nueva, selecciona esta opción. El sistema migra tus días automáticamente.</p>
            </div>
            <div style="background:rgba(234,179,8,0.06);border:1px solid rgba(234,179,8,0.18);border-radius:1rem;padding:1.5rem;">
                <div style="width:2.5rem;height:2.5rem;background:rgba(234,179,8,0.15);border-radius:0.75rem;display:flex;align-items:center;justify-content:center;margin-bottom:0.875rem;">
                    <svg width="18" height="18" fill="none" stroke="#fbbf24" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h4 style="font-size:0.95rem;font-weight:800;color:#fbbf24;margin-bottom:0.375rem;">Problema Menor</h4>
                <p style="font-size:0.82rem;color:rgba(148,163,184,0.7);line-height:1.6;">Si la cuenta tiene un problema técnico temporal o de acceso que no requiere cambio de correo. El tiempo se pausa y se retoma automáticamente.</p>
            </div>
        </div>

        <div class="tip-card">
            <svg width="20" height="20" fill="none" stroke="#34d399" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p style="font-size:0.875rem;color:rgba(52,211,153,0.85);line-height:1.6;"><strong>Tip:</strong> Ve a la sección <strong>Garantías</strong> en el menú, llena el formulario con todos los detalles y el administrador lo revisará lo antes posible. Recibirás una nota de respuesta en tu panel.</p>
        </div>
    </div>

    {{-- TIPS & ADVERTENCIAS --}}
    <div style="margin-bottom:2rem;">
        <div class="section-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Consejos Importantes
        </div>
        <div style="display:grid;gap:0.75rem;">
            <div class="tip-card">
                <svg width="20" height="20" fill="none" stroke="#34d399" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <p style="font-size:0.875rem;color:rgba(148,163,184,0.85);line-height:1.6;"><strong style="color:#34d399;">Espera el tiempo adecuado:</strong> Primero solicita la verificación en la plataforma (Netflix, Disney, etc.) y luego espera al menos 15-30 segundos antes de buscar el código aquí.</p>
            </div>
            <div class="tip-card">
                <svg width="20" height="20" fill="none" stroke="#34d399" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <p style="font-size:0.875rem;color:rgba(148,163,184,0.85);line-height:1.6;"><strong style="color:#34d399;">Copia el código rápido:</strong> Los códigos expiran en el tiempo indicado. Una vez que aparezca, usa el botón "Copiar" para pegarlo en la plataforma inmediatamente.</p>
            </div>
            <div class="warn-card">
                <svg width="20" height="20" fill="none" stroke="#f87171" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p style="font-size:0.875rem;color:rgba(252,165,165,0.85);line-height:1.6;"><strong style="color:#f87171;">¡Cuidado con los clics rápidos!</strong> El sistema detecta comportamiento sospechoso y puede bloquear tu cuenta temporalmente. No hagas más de 2 intentos en menos de 30 segundos.</p>
            </div>
        </div>
    </div>

    {{-- FAQ --}}
    <div style="margin-bottom:2rem;">
        <div class="section-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Preguntas Frecuentes
        </div>
        <div style="display:grid;gap:0.625rem;">
            @php
            $faqs = [
                ['q'=>'¿Por qué no encuentro el código?','a'=>'Asegúrate de haber solicitado la verificación en la plataforma primero. Luego espera 30-60 segundos y vuelve a intentarlo. Si el código no aparece después de 2 minutos, contacta a soporte.'],
                ['q'=>'¿Cuántas consultas puedo hacer por día?','a'=>'Tu plan tiene un límite de consultas diarias que puedes ver en la parte inferior de la página de consultas o en tu panel de inicio. Cuando se acabe el límite, se reinicia al día siguiente.'],
                ['q'=>'¿Qué hago si mi cuenta fue bloqueada por clics rápidos?','a'=>'El bloqueo es temporal (usualmente 15-30 minutos). Si persiste, contacta al administrador a través de la sección de Garantías explicando la situación.'],
                ['q'=>'¿Cómo solicito una garantía o reemplazo?','a'=>'Ve al menú "Garantías", rellena el formulario indicando si es un Reemplazo (cuenta caída) o Problema Menor, describe el problema con detalle y envía la solicitud. El admin la revisará pronto.'],
                ['q'=>'¿El código que veo es seguro? ¿Alguien más puede verlo?','a'=>'Sí, es seguro. Solo tú puedes ver tu código. El sistema lo muestra brevemente y luego lo destruye automáticamente por seguridad. Nunca compartas tu código con nadie.'],
            ];
            @endphp
            @foreach($faqs as $i => $faq)
            <div class="faq-item">
                <div class="faq-q" onclick="toggleFaq({{ $i }})">
                    <span>{{ $faq['q'] }}</span>
                    <svg id="faq-icon-{{ $i }}" class="faq-icon" width="18" height="18" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div id="faq-answer-{{ $i }}" class="faq-a">{{ $faq['a'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div style="background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(236,72,153,0.1));border:1px solid rgba(168,85,247,0.25);border-radius:1.5rem;padding:2rem;text-align:center;">
        <h3 style="font-size:1.25rem;font-weight:800;color:white;margin-bottom:0.5rem;">¿Listo para consultar tu código?</h3>
        <p style="font-size:0.875rem;color:rgba(148,163,184,0.7);margin-bottom:1.25rem;">Sigue los pasos de esta guía y tendrás tu código en segundos.</p>
        <a href="{{ route('client.query') }}" style="display:inline-flex;align-items:center;gap:0.625rem;background:linear-gradient(135deg,#7c3aed,#a855f7,#ec4899);border:none;border-radius:0.875rem;color:white;font-weight:700;font-size:0.95rem;padding:0.875rem 2rem;text-decoration:none;box-shadow:0 4px 20px rgba(168,85,247,0.4);transition:all 0.2s;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Consultar Código Ahora
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFaq(i) {
    var answer = document.getElementById('faq-answer-' + i);
    var icon   = document.getElementById('faq-icon-' + i);
    if (!answer) return;
    var isOpen = answer.classList.contains('open');
    // Close all
    document.querySelectorAll('.faq-a').forEach(function(el){ el.classList.remove('open'); });
    document.querySelectorAll('.faq-icon').forEach(function(el){ el.classList.remove('open'); });
    if (!isOpen) {
        answer.classList.add('open');
        icon.classList.add('open');
    }
}
</script>
@endpush
