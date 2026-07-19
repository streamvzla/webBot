@extends('public.layouts.app')

@section('title', 'Consultar Código')

@section('styles')
<style>
    /* ===== HERO ===== */
    .pub-hero {
        text-align: center;
        margin-bottom: 2rem;
        padding: 1rem 0 0;
    }
    .pub-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(168,85,247,0.1);
        border: 1px solid rgba(168,85,247,0.25);
        border-radius: 2rem;
        padding: 0.35rem 0.875rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: rgba(168,85,247,0.9);
        margin-bottom: 1rem;
    }
    .pub-hero h1 {
        font-size: clamp(1.75rem, 5vw, 2.5rem);
        font-weight: 900;
        background: linear-gradient(135deg, #f1f5f9 0%, #a855f7 55%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.15;
        margin-bottom: 0.75rem;
    }
    .pub-hero p {
        font-size: 0.95rem;
        color: rgba(148,163,184,0.7);
        max-width: 440px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* ===== PLATFORM GRID ===== */
    .platform-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.875rem;
        margin-bottom: 0;
    }
    .plat-btn {
        background: rgba(255,255,255,0.03);
        border: 1.5px solid rgba(168,85,247,0.13);
        border-radius: 1rem;
        padding: 1.125rem 0.75rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.22s cubic-bezier(0.4,0,0.2,1);
        position: relative;
        overflow: hidden;
    }
    .plat-btn::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(168,85,247,0.1), rgba(236,72,153,0.05));
        opacity: 0;
        transition: opacity 0.22s;
    }
    .plat-btn:hover {
        border-color: rgba(168,85,247,0.45);
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(168,85,247,0.18);
    }
    .plat-btn:hover::before { opacity: 1; }
    .plat-btn.selected {
        border-color: #a855f7 !important;
        background: rgba(168,85,247,0.1) !important;
        box-shadow: 0 0 0 2px rgba(168,85,247,0.3), 0 10px 28px rgba(168,85,247,0.22) !important;
        transform: translateY(-3px);
    }
    .plat-btn.selected::after {
        content: '✓';
        position: absolute;
        top: 0.3rem; right: 0.4rem;
        width: 1.15rem; height: 1.15rem;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        border-radius: 50%;
        font-size: 0.65rem; font-weight: 900;
        color: white;
        display: flex; align-items: center; justify-content: center;
        line-height: 1.15rem;
    }
    .plat-btn .plat-icon {
        width: 3rem; height: 3rem;
        border-radius: 0.75rem;
        object-fit: cover;
        margin: 0 auto 0.625rem;
        display: block;
    }
    .plat-btn .plat-icon-fallback {
        width: 3rem; height: 3rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, rgba(124,58,237,0.25), rgba(236,72,153,0.15));
        border: 1px solid rgba(168,85,247,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        margin: 0 auto 0.625rem;
    }
    .plat-btn p {
        font-size: 0.8rem;
        font-weight: 600;
        color: rgba(226,232,240,0.9);
        position: relative; z-index: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===== INPUT ===== */
    .field-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(168,85,247,0.85);
        margin-bottom: 0.5rem;
    }
    .field-input {
        width: 100%;
        background: rgba(255,255,255,0.04);
        border: 1.5px solid rgba(168,85,247,0.15);
        border-radius: 0.75rem;
        padding: 0.875rem 1rem 0.875rem 2.75rem;
        color: white;
        font-size: 0.9rem;
        font-family: 'Inter', sans-serif;
        outline: none;
        transition: all 0.2s;
    }
    .field-input::placeholder { color: rgba(148,163,184,0.4); }
    .field-input:focus {
        border-color: rgba(168,85,247,0.5);
        background: rgba(168,85,247,0.05);
        box-shadow: 0 0 0 3px rgba(168,85,247,0.1);
    }
    .input-wrap { position: relative; }
    .input-icon {
        position: absolute;
        left: 0.875rem; top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* ===== DIVIDER ===== */
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(168,85,247,0.2), transparent);
        margin: 1.5rem 0;
    }

    /* ===== STEP LABEL ===== */
    .step-lbl {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: rgba(168,85,247,0.7);
        margin-bottom: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .step-lbl .step-num {
        width: 1.35rem; height: 1.35rem;
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.65rem; color: white; font-weight: 900;
        flex-shrink: 0;
    }

    /* ===== SUBMIT BUTTON ===== */
    .btn-search {
        width: 100%;
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
        border: none;
        border-radius: 0.875rem;
        color: white;
        font-size: 1rem;
        font-weight: 800;
        font-family: 'Inter', sans-serif;
        padding: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        letter-spacing: 0.02em;
        box-shadow: 0 4px 20px rgba(168,85,247,0.4);
        transition: all 0.25s;
    }
    .btn-search:hover:not(:disabled) {
        box-shadow: 0 8px 30px rgba(168,85,247,0.55);
        transform: translateY(-2px);
        filter: brightness(1.08);
    }
    .btn-search:disabled {
        background: rgba(255,255,255,0.05);
        box-shadow: none;
        cursor: not-allowed;
        color: rgba(255,255,255,0.25);
        transform: none;
        filter: none;
    }

    /* ===== RESULT SUCCESS ===== */
    .result-card {
        background: linear-gradient(135deg, rgba(52,211,153,0.06), rgba(16,185,129,0.04));
        border: 1.5px solid rgba(52,211,153,0.2);
        border-radius: 1.25rem;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .result-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #34d399, transparent);
    }
    .code-box {
        display: inline-block;
        background: rgba(0,0,0,0.5);
        border: 1.5px solid rgba(168,85,247,0.35);
        border-radius: 1rem;
        padding: 1.25rem 2.5rem;
        font-family: 'Courier New', monospace;
        font-size: clamp(2rem, 8vw, 3.5rem);
        font-weight: 900;
        letter-spacing: 0.3em;
        color: white;
        text-shadow: 0 0 30px rgba(168,85,247,0.8);
        margin: 1rem 0;
        word-break: break-all;
    }

    /* ===== EMAIL BODY ===== */
    .email-body {
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        word-wrap: break-word;
        padding: 1rem;
        border-radius: 0.5rem;
        max-height: 380px;
        overflow-y: auto;
        text-align: left;
    }
    .email-html { font-family: inherit; white-space: normal; padding: 1.5rem; }
    .email-html img { max-width: 100%; height: auto; }
    .email-html table { width: 100%; border-collapse: collapse; }
    .email-html td { padding: 8px; }
    .email-html a:not([style*="color:"]) { color: #0066cc !important; }

    /* ===== SPINNER ===== */
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner {
        width: 2.75rem; height: 2.75rem;
        border: 3px solid rgba(168,85,247,0.1);
        border-top-color: #a855f7;
        border-right-color: #ec4899;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: 0 auto 1rem;
    }

    /* ===== WARN BOX ===== */
    .warn-box {
        background: rgba(239,68,68,0.06);
        border: 1px solid rgba(239,68,68,0.18);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.78rem;
        color: rgba(252,165,165,0.8);
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    /* ===== INFO BOTTOM ===== */
    .info-bottom {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(168,85,247,0.1);
        border-radius: 1rem;
        padding: 1.25rem;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .info-bottom p { font-size: 0.85rem; color: rgba(148,163,184,0.75); }
    .info-bottom a {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        background: linear-gradient(135deg, rgba(124,58,237,0.25), rgba(236,72,153,0.15));
        border: 1.5px solid rgba(168,85,247,0.3);
        border-radius: 0.625rem;
        color: #c4b5fd;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.5rem 0.875rem;
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .info-bottom a:hover { border-color: rgba(168,85,247,0.6); color: white; background: linear-gradient(135deg, rgba(124,58,237,0.4), rgba(236,72,153,0.25)); }

    /* ===== BTN SECONDARY ===== */
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        color: rgba(148,163,184,0.9);
        font-size: 0.875rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        padding: 0.75rem 1.5rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        margin-top: 0.75rem;
        width: 100%;
    }
    .btn-secondary:hover { background: rgba(168,85,247,0.08); border-color: rgba(168,85,247,0.25); color: white; }

    /* ===== COPY BTN ===== */
    .btn-copy {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(168,85,247,0.12);
        border: 1.5px solid rgba(168,85,247,0.3);
        border-radius: 0.75rem;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
        padding: 0.75rem 2rem;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 0.5rem;
    }
    .btn-copy:hover { background: rgba(168,85,247,0.22); border-color: rgba(168,85,247,0.55); box-shadow: 0 4px 15px rgba(168,85,247,0.25); }

    /* ===== COUNTDOWN ===== */
    #pub-countdown { color: #a855f7; font-weight: 800; }
</style>
@endsection

@section('content')

    {{-- ===== LIVEWIRE FORM ===== --}}
    @livewire('public-query-form')


@endsection
