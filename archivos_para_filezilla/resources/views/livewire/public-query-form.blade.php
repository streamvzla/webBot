<div id="pqf-root">

{{-- ===== ESTILOS ULTRA PREMIUM ===== --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');

    /* ===== RESET & ROOT ===== */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --purple: #a855f7;
        --purple-dark: #7c3aed;
        --pink: #ec4899;
        --green: #10b981;
        --green-light: #34d399;
        --bg-card: rgba(255,255,255,0.02);
        --border: rgba(168,85,247,0.15);
        --text-muted: rgba(148,163,184,0.75);
    }

    /* ===== FULL VIEWPORT LAYOUT (NO SCROLL) ===== */
    html, body {
        height: 100%;
        overflow: hidden !important;
    }

    /* Override del layout padre para esta página */
    .main-content {
        padding: 0 !important;
        display: flex !important;
        align-items: stretch !important;
        height: calc(100vh - 4rem) !important;
        overflow: hidden !important;
    }
    .container {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        height: 100% !important;
    }
    /* Ocultar footer en esta página */
    footer { display: none !important; }

    /* ===== ENTERPRISE LAYOUT ===== */
    .pq-shell {
        display: grid;
        grid-template-columns: 1fr 1fr;
        height: 100%;
        width: 100%;
        font-family: 'Inter', sans-serif;
        overflow: hidden;
    }
    @media (max-width: 900px) {
        .pq-shell {
            grid-template-columns: 1fr;
            overflow-y: auto !important;
        }
        html, body { overflow: auto !important; }
        .main-content { height: auto !important; overflow: visible !important; }
        .pq-right-panel { display: none !important; }
    }

    /* ===== LEFT PANEL — FORMULARIO ===== */
    .pq-left {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1.5rem 3rem;
        position: relative;
        overflow: hidden;
        background: rgba(0,0,0,0.3);
        border-right: 1px solid rgba(168,85,247,0.12);
    }
    .pq-left::before {
        content: '';
        position: absolute;
        top: -30%;
        left: -20%;
        width: 70%;
        height: 70%;
        background: radial-gradient(circle, rgba(124,58,237,0.18) 0%, transparent 65%);
        pointer-events: none;
        animation: orb-drift 8s ease-in-out infinite alternate;
    }
    .pq-left::after {
        content: '';
        position: absolute;
        bottom: -20%;
        right: -10%;
        width: 50%;
        height: 50%;
        background: radial-gradient(circle, rgba(236,72,153,0.12) 0%, transparent 65%);
        pointer-events: none;
        animation: orb-drift 10s ease-in-out infinite alternate-reverse;
    }
    @keyframes orb-drift {
        from { transform: translate(0,0) scale(1); }
        to   { transform: translate(30px, 20px) scale(1.08); }
    }

    .pq-inner {
        width: 100%;
        max-width: 440px;
        position: relative;
        z-index: 2;
    }

    /* ===== BADGE ===== */
    .pq-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: rgba(168,85,247,0.12);
        border: 1px solid rgba(168,85,247,0.3);
        border-radius: 100px;
        padding: 0.35rem 1rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #c4b5fd;
        margin-bottom: 1.5rem;
    }
    .pq-badge-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--green-light);
        box-shadow: 0 0 8px var(--green-light);
        animation: pulse-dot 2s ease-in-out infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }

    /* ===== HEADING ===== */
    .pq-title {
        font-size: clamp(1.8rem, 3.5vw, 2.6rem);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -0.03em;
        background: linear-gradient(135deg, #f8fafc 0%, #e2d9f3 40%, var(--purple) 70%, var(--pink) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.75rem;
    }
    .pq-subtitle {
        font-size: 0.92rem;
        color: var(--text-muted);
        line-height: 1.65;
        margin-bottom: 2.25rem;
        max-width: 340px;
    }

    /* ===== ALERTS DE ESTADO ===== */
    .pq-alert {
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1.75rem;
        line-height: 1.5;
        animation: slide-in 0.35s cubic-bezier(.22,1,.36,1);
    }
    .pq-alert-icon { flex-shrink: 0; margin-top: 1px; }
    .pq-alert strong { display: block; font-weight: 700; margin-bottom: 0.2rem; font-size: 0.92rem; }
    .pq-alert.alert-error  { background: rgba(239,68,68,0.08);  border: 1px solid rgba(239,68,68,0.25);  color: rgba(252,165,165,0.95); }
    .pq-alert.alert-warn   { background: rgba(234,179,8,0.08);  border: 1px solid rgba(234,179,8,0.25);  color: rgba(253,224,71,0.95); }
    @keyframes slide-in {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ===== INPUT GROUP ===== */
    .pq-input-group {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
        margin-bottom: 0.75rem;
    }
    .pq-field-wrap { position: relative; }
    .pq-field-icon {
        position: absolute;
        left: 1.1rem; top: 50%;
        transform: translateY(-50%);
        color: rgba(168,85,247,0.6);
        pointer-events: none;
        transition: color 0.2s;
    }
    .pq-input {
        width: 100%;
        background: rgba(255,255,255,0.04);
        border: 1.5px solid rgba(168,85,247,0.2);
        border-radius: 0.875rem;
        padding: 1rem 1.1rem 1rem 3rem;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: all 0.25s;
        backdrop-filter: blur(4px);
    }
    .pq-input::placeholder { color: rgba(148,163,184,0.35); font-weight: 400; }
    .pq-input:focus {
        border-color: var(--purple);
        background: rgba(168,85,247,0.06);
        box-shadow: 0 0 0 4px rgba(168,85,247,0.12), 0 1px 20px rgba(168,85,247,0.08);
    }
    .pq-input:focus + .pq-field-icon { color: var(--purple); }

    .pq-error {
        font-size: 0.8rem;
        color: #fca5a5;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 500;
        animation: slide-in 0.25s ease;
    }

    /* ===== SEARCH BUTTON ===== */
    .pq-btn {
        width: 100%;
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
        background-size: 200% 200%;
        border: none;
        border-radius: 0.875rem;
        color: white;
        font-size: 1rem;
        font-weight: 800;
        font-family: 'Inter', sans-serif;
        padding: 1.05rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        letter-spacing: 0.01em;
        box-shadow: 0 6px 24px rgba(168,85,247,0.45);
        transition: all 0.3s cubic-bezier(.22,1,.36,1);
        animation: gradient-shift 4s ease infinite;
    }
    @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .pq-btn:hover:not(:disabled) {
        box-shadow: 0 10px 35px rgba(168,85,247,0.6);
        transform: translateY(-2px) scale(1.01);
    }
    .pq-btn:active:not(:disabled) { transform: translateY(0) scale(0.99); }
    .pq-btn:disabled {
        background: rgba(255,255,255,0.07) !important;
        box-shadow: none !important;
        color: rgba(255,255,255,0.2) !important;
        cursor: not-allowed !important;
        transform: none !important;
    }
    .pq-spinner {
        width: 18px; height: 18px;
        border: 2.5px solid rgba(255,255,255,0.2);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ===== SECURE BADGE ===== */
    .pq-secure {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        color: rgba(148,163,184,0.4);
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 1rem;
    }

    /* ===== RESULTADO: CÓDIGO ENCONTRADO ===== */
    .pq-result {
        animation: result-appear 0.5s cubic-bezier(.22,1,.36,1);
    }
    @keyframes result-appear {
        from { opacity: 0; transform: scale(0.96) translateY(12px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .pq-result-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .pq-plat-logo {
        width: 3.5rem; height: 3.5rem;
        border-radius: 1rem;
        object-fit: cover;
        box-shadow: 0 8px 25px rgba(168,85,247,0.3);
        border: 1.5px solid rgba(168,85,247,0.3);
    }
    .pq-plat-icon-fallback {
        width: 3.5rem; height: 3.5rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(236,72,153,0.15));
        border: 1.5px solid rgba(168,85,247,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }

    .pq-success-title {
        font-size: 1.8rem;
        font-weight: 900;
        background: linear-gradient(135deg, var(--green-light), var(--green));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
        margin-bottom: 0.2rem;
    }
    .pq-recv-time {
        font-size: 0.8rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* ===== CODE BOX ===== */
    .pq-code-container {
        background: rgba(0,0,0,0.5);
        border: 1.5px solid rgba(52,211,153,0.2);
        border-radius: 1.25rem;
        padding: 1.75rem;
        text-align: center;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .pq-code-container::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--green-light), transparent);
    }
    .pq-code-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: rgba(52,211,153,0.6);
        margin-bottom: 0.875rem;
    }
    .pq-code-value {
        font-family: 'Courier New', 'JetBrains Mono', monospace;
        font-size: clamp(2.25rem, 7vw, 3.5rem);
        font-weight: 900;
        letter-spacing: 0.2em;
        color: #ffffff;
        text-shadow: 0 0 40px rgba(168,85,247,0.7), 0 0 80px rgba(168,85,247,0.3);
        word-break: break-all;
        line-height: 1.1;
        cursor: pointer;
        user-select: all;
        transition: text-shadow 0.3s;
    }
    .pq-code-value:hover {
        text-shadow: 0 0 50px rgba(168,85,247,1), 0 0 100px rgba(168,85,247,0.5);
    }

    /* ===== LINK BTN ===== */
    .pq-link-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        background: linear-gradient(135deg, #059669, var(--green));
        color: white;
        font-weight: 800;
        font-size: 1rem;
        font-family: 'Inter', sans-serif;
        padding: 1.05rem 2rem;
        border-radius: 1rem;
        text-decoration: none;
        box-shadow: 0 6px 24px rgba(16,185,129,0.4);
        transition: all 0.25s;
        margin-bottom: 1.25rem;
    }
    .pq-link-btn:hover {
        box-shadow: 0 10px 35px rgba(16,185,129,0.55);
        transform: translateY(-2px);
    }

    /* ===== ACTION BUTTONS ===== */
    .pq-actions { display: flex; flex-direction: column; gap: 0.75rem; }
    .pq-copy-btn {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        background: rgba(168,85,247,0.1);
        border: 1.5px solid rgba(168,85,247,0.3);
        border-radius: 0.875rem;
        color: #c4b5fd;
        font-size: 0.9rem; font-weight: 700; font-family: 'Inter', sans-serif;
        padding: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
    }
    .pq-copy-btn:hover { background: rgba(168,85,247,0.2); border-color: var(--purple); color: white; box-shadow: 0 4px 15px rgba(168,85,247,0.25); }
    .pq-copy-btn.copied { background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.4); color: var(--green-light); }

    .pq-reset-btn {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 0.875rem;
        color: var(--text-muted);
        font-size: 0.875rem; font-weight: 600; font-family: 'Inter', sans-serif;
        padding: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
    }
    .pq-reset-btn:hover { background: rgba(168,85,247,0.06); border-color: rgba(168,85,247,0.2); color: #e2e8f0; }

    /* ===== RIGHT PANEL ===== */
    .pq-right-panel {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 1.5rem 3rem;
        position: relative;
        overflow: hidden;
    }
    .pq-right-panel::before {
        content: '';
        position: absolute;
        top: 10%; right: -15%;
        width: 60%;
        height: 60%;
        background: radial-gradient(circle, rgba(236,72,153,0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .pq-right-panel::after {
        content: '';
        position: absolute;
        bottom: 5%; left: 10%;
        width: 45%;
        height: 45%;
        background: radial-gradient(circle, rgba(99,102,241,0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .pq-right-inner { position: relative; z-index: 1; }

    /* ===== SECTION LABEL ===== */
    .pq-section-lbl {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: rgba(168,85,247,0.5);
        margin-bottom: 1.25rem;
        display: flex; align-items: center; gap: 0.75rem;
    }
    .pq-section-lbl::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(168,85,247,0.2), transparent);
    }

    /* ===== FEATURE CARDS ===== */
    .pq-features { display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 1.5rem; }
    .pq-feat-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1.1rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: border-color 0.25s, transform 0.25s;
    }
    .pq-feat-card:hover {
        border-color: rgba(168,85,247,0.2);
        transform: translateX(4px);
    }
    .pq-feat-icon {
        width: 2.5rem; height: 2.5rem;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .pq-feat-icon.green  { background: rgba(16,185,129,0.12);  border: 1px solid rgba(16,185,129,0.2); }
    .pq-feat-icon.purple { background: rgba(168,85,247,0.12); border: 1px solid rgba(168,85,247,0.2); }
    .pq-feat-icon.blue   { background: rgba(99,102,241,0.12);  border: 1px solid rgba(99,102,241,0.2); }
    .pq-feat-title { font-size: 0.88rem; font-weight: 700; color: #f1f5f9; margin-bottom: 0.2rem; }
    .pq-feat-desc  { font-size: 0.78rem; color: var(--text-muted); line-height: 1.55; }

    /* ===== STATS ROW ===== */
    .pq-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.875rem;
        margin-bottom: 2rem;
    }
    .pq-stat {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(168,85,247,0.1);
        border-radius: 1rem;
        padding: 1rem;
        text-align: center;
    }
    .pq-stat-val {
        font-size: 1.4rem; font-weight: 900;
        background: linear-gradient(135deg, var(--purple), var(--pink));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
    }
    .pq-stat-lbl { font-size: 0.68rem; color: var(--text-muted); font-weight: 600; margin-top: 0.3rem; text-transform: uppercase; letter-spacing: 0.06em; }

    /* ===== TRUST BAR ===== */
    .pq-trust {
        background: rgba(168,85,247,0.05);
        border: 1px solid rgba(168,85,247,0.1);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    .pq-trust strong { color: #c4b5fd; }

    /* ===== COUNTDOWN (cuando hay código) ===== */
    .pq-countdown-bar {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(168,85,247,0.1);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }
    .pq-countdown-timer { color: var(--purple); font-weight: 800; font-size: 0.95rem; font-family: 'Courier New', monospace; }
    .pq-countdown-bar .progress-track {
        width: 100%; height: 3px;
        background: rgba(168,85,247,0.1);
        border-radius: 99px;
        margin-top: 0.5rem;
        overflow: hidden;
    }
    .pq-countdown-bar .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--purple), var(--pink));
        border-radius: 99px;
        transition: width 1s linear;
    }
</style>

{{-- ===== ESTRUCTURA PRINCIPAL ===== --}}
<div class="pq-shell">

    {{-- ============ COLUMNA IZQUIERDA — FORMULARIO / RESULTADO ============ --}}
    <div class="pq-left">
        <div class="pq-inner">

            @if($resultStatus === 'success' && $resultData)
            {{-- ===== RESULTADO ENCONTRADO ===== --}}
            @php
                $code = $resultData['code'] ?? '';
                $isLink = preg_match('/^(http|https):\/\/[^ "]+$/', $code);
                $isCode = !$isLink && !empty($code);
                $displaySeconds = $resultData['expires_in'] ?? 60;
            @endphp

            <div class="pq-result">
                {{-- Header con plataforma --}}
                <div class="pq-result-header">
                    @if($resultData['platform_logo'])
                        <img src="{{ asset('storage/' . $resultData['platform_logo']) }}" alt="{{ $resultData['platform_name'] }}" class="pq-plat-logo">
                    @else
                        <div class="pq-plat-icon-fallback">📺</div>
                    @endif
                    <div>
                        <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:rgba(148,163,184,0.5);margin-bottom:0.15rem;">Plataforma Detectada</p>
                        <h3 style="font-size:1.1rem;font-weight:800;color:#fff;">{{ $resultData['platform_name'] }}</h3>
                    </div>
                    <div style="margin-left:auto;">
                        <span style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);border-radius:100px;padding:0.3rem 0.875rem;font-size:0.68rem;font-weight:700;color:#34d399;text-transform:uppercase;letter-spacing:0.08em;">
                            ✓ Encontrado
                        </span>
                    </div>
                </div>

                {{-- Título éxito --}}
                <h2 class="pq-success-title" style="margin-bottom:0.4rem;">¡Código Encontrado!</h2>
                <p class="pq-recv-time" style="margin-bottom:1.5rem;">
                    <svg width="13" height="13" fill="none" stroke="#34d399" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Recibido: <strong style="color:#34d399;margin-left:0.25rem;">{{ $resultData['received_at'] }}</strong>
                </p>

                {{-- Countdown --}}
                <div class="pq-countdown-bar" id="pq-cdb">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Expira en</span>
                    </div>
                    <span class="pq-countdown-timer" id="pq-timer">--:--</span>
                </div>
                <div class="progress-track" style="margin-bottom:1.25rem;">
                    <div class="progress-fill" id="pq-progress" style="width:100%;"></div>
                </div>

                {{-- Código o Link --}}
                @if($isCode)
                <div class="pq-code-container" onclick="copyCode('{{ $code }}')" title="Clic para copiar" style="cursor:pointer;">
                    <p class="pq-code-label">Tu Código de Seguridad — Clic para copiar</p>
                    <div class="pq-code-value" id="pq-code-display">{{ $code }}</div>
                </div>
                @elseif($isLink)
                <a href="{{ $code }}" target="_blank" class="pq-link-btn" style="margin-bottom:1.25rem;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Abrir Enlace Mágico
                </a>
                @endif

                {{-- Acciones --}}
                <div class="pq-actions">
                    @if($isCode)
                    <button class="pq-copy-btn" id="pq-copy-btn" onclick="copyCode('{{ $code }}')">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="pq-copy-lbl">Copiar Código</span>
                    </button>
                    @endif
                    <button class="pq-reset-btn" wire:click="resetForm">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Consultar Otro Correo
                    </button>
                </div>
            </div>

            @else
            {{-- ===== FORMULARIO DE BÚSQUEDA ===== --}}

            {{-- Badge --}}
            <div>
                <div class="pq-badge">
                    <span class="pq-badge-dot"></span>
                    Sistema Centinela Activo
                </div>
            </div>

            {{-- Título --}}
            <h1 class="pq-title">Consulta<br>tu Código</h1>
            <p class="pq-subtitle">Ingresa tu correo y nuestro sistema lo detectará instantáneamente, sin esperas.</p>

            {{-- Alertas de estado --}}
            @if($resultStatus === 'not_authorized')
            <div class="pq-alert alert-error">
                <div class="pq-alert-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div><strong>Acceso Denegado</strong>Este correo no está autorizado. Asegúrate de tener una suscripción activa o contacta a soporte.</div>
            </div>
            @elseif($resultStatus === 'not_found')
            <div class="pq-alert alert-warn">
                <div class="pq-alert-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div><strong>Sin códigos recientes</strong>Aún no hay códigos para este correo. Espera 2 minutos e intenta de nuevo.</div>
            </div>
            @elseif($resultStatus === 'error')
            <div class="pq-alert alert-error">
                <div class="pq-alert-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>Ocurrió un error al intentar la consulta. Inténtalo de nuevo en unos segundos.</div>
            </div>
            @endif

            {{-- Formulario --}}
            <form wire:submit.prevent="submit" class="pq-input-group" style="gap:1rem;">
                <div>
                    <div class="pq-field-wrap">
                        <input
                            type="email"
                            wire:model.defer="email"
                            class="pq-input"
                            id="pq-email"
                            placeholder="tu@correo.com"
                            autocomplete="email"
                            spellcheck="false"
                        >
                        <div class="pq-field-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    @error('email')
                    <p class="pq-error" style="margin-top:0.5rem;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <button type="submit" class="pq-btn" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <div class="pq-spinner" wire:loading style="display:none;" wire:loading.style="display:block;"></div>
                    <span wire:loading.remove>Buscar Código</span>
                    <span wire:loading>Buscando...</span>
                </button>
            </form>

            <div class="pq-secure">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Protegido por Sistema Centinela
            </div>

            @endif
        </div>
    </div>

    {{-- ============ COLUMNA DERECHA — INFO PANEL ============ --}}
    <div class="pq-right-panel">
        <div class="pq-right-inner">

            <div class="pq-section-lbl">Tu Código Enterprise Platform</div>

            {{-- Stats --}}
            <div class="pq-stats">
                <div class="pq-stat">
                    <div class="pq-stat-val">&lt;1s</div>
                    <div class="pq-stat-lbl">Respuesta</div>
                </div>
                <div class="pq-stat">
                    <div class="pq-stat-val">99.9%</div>
                    <div class="pq-stat-lbl">Uptime</div>
                </div>
                <div class="pq-stat">
                    <div class="pq-stat-val">256</div>
                    <div class="pq-stat-lbl">Encriptado</div>
                </div>
            </div>

            {{-- Feature cards --}}
            <div class="pq-features">
                <div class="pq-feat-card">
                    <div class="pq-feat-icon green">⚡</div>
                    <div>
                        <div class="pq-feat-title">Detección Instantánea</div>
                        <div class="pq-feat-desc">Nuestro Centinela monitorea tu bandeja en tiempo real. El código aparece en segundos, sin recargar.</div>
                    </div>
                </div>
                <div class="pq-feat-card">
                    <div class="pq-feat-icon purple">🛡️</div>
                    <div>
                        <div class="pq-feat-title">Autodestrucción Segura</div>
                        <div class="pq-feat-desc">Los códigos se evaporan automáticamente. Nadie más puede interceptarlos después de que los veas.</div>
                    </div>
                </div>
                <div class="pq-feat-card">
                    <div class="pq-feat-icon blue">📋</div>
                    <div>
                        <div class="pq-feat-title">Tips para éxito</div>
                        <div class="pq-feat-desc">Espera <strong style="color:#e2e8f0;">al menos 2 min</strong> y asegúrate de escribir el correo <strong style="color:#e2e8f0;">exactamente igual</strong> al registrado.</div>
                    </div>
                </div>
            </div>

            {{-- Trust bar --}}
            <div class="pq-trust">
                <svg width="18" height="18" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Tu privacidad está protegida. <strong>Nunca almacenamos</strong> códigos en texto plano.</span>
            </div>

        </div>
    </div>

</div>

{{-- ===== SCRIPTS ===== --}}
<script>
    // Copy to clipboard
    function copyCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            const btn = document.getElementById('pq-copy-btn');
            const lbl = document.getElementById('pq-copy-lbl');
            if (btn && lbl) {
                btn.classList.add('copied');
                lbl.textContent = '¡Copiado!';
                setTimeout(() => {
                    btn.classList.remove('copied');
                    lbl.textContent = 'Copiar Código';
                }, 2500);
            }
        }).catch(() => {
            // Fallback
            const el = document.createElement('textarea');
            el.value = code;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        });
    }

    // Countdown timer — compatible con Livewire
    @if($resultStatus === 'success' && $resultData)
    window._pqTimerTotal = {{ $displaySeconds ?? 60 }};
    window._pqTimerActive = true;

    function pqStartTimer() {
        if (!window._pqTimerActive) return;
        const total = window._pqTimerTotal;
        let remaining = total;
        // Limpiar cualquier timer previo
        if (window._pqTimerId) clearInterval(window._pqTimerId);

        function tick() {
            const timerEl = document.getElementById('pq-timer');
            const progressEl = document.getElementById('pq-progress');
            if (!timerEl) return;
            const m = String(Math.floor(remaining / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            timerEl.textContent = m + ':' + s;
            if (progressEl) {
                progressEl.style.width = ((remaining / total) * 100) + '%';
                // Color rojo cuando quedan menos de 10 segundos
                if (remaining <= 10) {
                    progressEl.style.background = 'linear-gradient(90deg, #ef4444, #f97316)';
                }
            }
            if (remaining <= 0) {
                timerEl.textContent = '00:00';
                timerEl.style.color = '#ef4444';
                if (progressEl) progressEl.style.width = '0%';
                clearInterval(window._pqTimerId);
                return;
            }
            remaining--;
        }
        tick(); // primer tick inmediato
        window._pqTimerId = setInterval(tick, 1000);
    }

    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', pqStartTimer);
    } else {
        // DOM ya listo (Livewire re-render)
        setTimeout(pqStartTimer, 50);
    }
    // Hook para re-renders de Livewire
    document.addEventListener('livewire:load', pqStartTimer);
    @endif
</script>

</div>
