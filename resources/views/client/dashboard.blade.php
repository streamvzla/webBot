@extends('client.layouts.app')

@section('title', 'Panel de Cliente')

@section('styles')
<style>
    /* ═══════════════════════════════════
       DASHBOARD ENTERPRISE v3.0
       Paleta idéntica al Login:
       BG: #050510 | Card: rgba(14,8,35,0.98)
       Violet: #7c3aed #a855f7 | Pink: #ec4899
    ═══════════════════════════════════ */

    /* === HERO WELCOME — COMO MIS GARANTIAS === */
    .dash-hero {
        background: rgba(255,255,255,0.025);
        border: 1px solid rgba(255,255,255,0.08);
        border-top: 2px solid rgba(168,85,247,0.5);
        border-radius: 1.5rem;
        padding: 1.5rem 1.25rem;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
        backdrop-filter: blur(12px);
        transition: border-color 0.3s;
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: center;
    }
    @media (min-width: 640px) {
        .dash-hero { padding: 2rem 2.5rem; }
    }
    .dash-hero:hover { border-top-color: rgba(168,85,247,0.8); }
    @media (min-width: 900px) {
        .dash-hero { grid-template-columns: 1fr 1px auto; gap: 2.5rem; }
    }
    .dash-hero::before { display: none; }
    .dash-hero-bottom-glow, .dash-hero-orb-tl, .dash-hero-orb-br { display: none; }

    /* Left block */
    .hero-left {
        display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem;
        position: relative; z-index: 1;
    }
    @media (min-width: 640px) {
        .hero-left { flex-direction: row; text-align: left; align-items: center; gap: 1.5rem; }
    }
    .hero-avatar-wrap {
        position: relative; flex-shrink: 0;
        width: 4.5rem; height: 4.5rem; border-radius: 50%;
        background: linear-gradient(135deg, rgba(168,85,247,0.4) 0%, rgba(236,72,153,0.2) 100%);
        padding: 4px; box-shadow: 0 0 25px rgba(168,85,247,0.25);
        z-index: 2;
    }
    @media (min-width: 640px) { .hero-avatar-wrap { width: 5.5rem; height: 5.5rem; } }
    .hero-avatar {
        width: 100%; height: 100%; object-fit: cover; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; color: white; font-size: 2rem;
    }
    .hero-identity { flex: 1; min-width: 0; width: 100%; }
    .hero-greeting {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.15em; color: rgba(168,85,247,0.8);
        margin-bottom: 0.35rem;
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    }
    @media (min-width: 640px) { .hero-greeting { justify-content: flex-start; } }
    .hero-name {
        font-size: 1.35rem; font-weight: 800; line-height: 1.2;
        margin-bottom: 0.15rem;
        background: linear-gradient(135deg, #ffffff 0%, rgba(196,181,253,0.85) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
        white-space: normal; overflow: hidden; text-overflow: ellipsis;
    }
    @media (min-width: 640px) { .hero-name { font-size: 2rem; font-weight: 900; white-space: nowrap; line-height: 1.05; } }
    .hero-email { font-size: 0.78rem; color: rgba(148,163,184,0.45); margin-bottom: 0.75rem; word-break: break-all; }
    .hero-badges { display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap; }
    @media (min-width: 640px) { .hero-badges { justify-content: flex-start; } }

    /* Divider */
    .hero-divider {
        display: none;
        background: linear-gradient(180deg, transparent 0%, rgba(168,85,247,0.2) 30%, rgba(168,85,247,0.2) 70%, transparent 100%);
        align-self: stretch; min-height: 80px;
    }
    @media (min-width: 900px) { .hero-divider { display: block; } }

    /* Right block */
    .hero-right {
        display: flex; flex-direction: row; align-items: center; justify-content: center; flex-wrap: wrap;
        gap: 1.5rem; position: relative; z-index: 1; width: 100%;
    }
    @media (min-width: 900px) { .hero-right { flex-direction: column; align-items: flex-end; gap: 0.875rem; width: auto; flex-wrap: nowrap; } }

    .hero-clock-wrap { text-align: center; }
    @media (min-width: 640px) { .hero-clock-wrap { text-align: left; } }
    @media (min-width: 900px) { .hero-clock-wrap { text-align: right; } }

    .hero-usage {
        background: rgba(168,85,247,0.04);
        border: 1px solid rgba(168,85,247,0.12);
        border-radius: 0.875rem;
        padding: 0.75rem 1.125rem;
        min-width: 180px;
    }
    .hero-usage-bar-bg {
        background: rgba(0,0,0,0.5); border-radius: 9999px;
        height: 4px; overflow: hidden; margin: 0.35rem 0 0.25rem;
    }
    .hero-usage-bar-fill {
        height: 100%; border-radius: 9999px;
        background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899);
        box-shadow: 0 0 8px rgba(168,85,247,0.5);
    }

    /* === STATUS BADGES === */
    .status-active {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(52,211,153,0.06); border: 1px solid rgba(52,211,153,0.2);
        padding: 0.4rem 1rem; border-radius: 9999px;
        font-size: 0.72rem; font-weight: 700;
        color: #34d399; text-transform: uppercase; letter-spacing: 0.07em;
    }
    .status-inactive {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);
        padding: 0.4rem 1rem; border-radius: 9999px;
        font-size: 0.72rem; font-weight: 700;
        color: #f87171; text-transform: uppercase; letter-spacing: 0.07em;
    }
    .badge-mode {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: rgba(168,85,247,0.08); border: 1px solid rgba(168,85,247,0.2);
        padding: 0.4rem 0.9rem; border-radius: 9999px;
        font-size: 0.68rem; font-weight: 700;
        color: rgba(196,181,253,0.85); text-transform: uppercase; letter-spacing: 0.07em;
    }
    .status-dot {
        width: 0.45rem; height: 0.45rem; border-radius: 50%;
        display: inline-block; animation: pulse-dot 2s infinite;
    }
    .status-dot.green { background: #34d399; box-shadow: 0 0 6px #34d399; }
    .status-dot.red   { background: #f87171; box-shadow: 0 0 6px #f87171; }
    .status-dot.violet { background: #a855f7; box-shadow: 0 0 6px #a855f7; }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.6; transform: scale(0.8); }
    }

    /* === ALERT BANNERS — GOD LEVEL === */
    .alert-banner {
        border-radius: 1.125rem;
        padding: 1rem 1.5rem;
        display: flex; align-items: flex-start; gap: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid;
        border-left-width: 4px;
        position: relative; overflow: hidden;
        animation: alert-slide-in 0.4s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes alert-slide-in {
        from { opacity: 0; transform: translateX(-20px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .alert-warning {
        background: linear-gradient(135deg, rgba(251,191,36,0.07), rgba(251,191,36,0.02));
        border-color: rgba(251,191,36,0.2);
        border-left-color: #fbbf24;
    }
    .alert-cooldown {
        background: linear-gradient(135deg, rgba(236,72,153,0.07), rgba(236,72,153,0.02));
        border-color: rgba(236,72,153,0.2);
        border-left-color: #ec4899;
    }

    /* === GLASS CARDS === */
    .glass-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.25rem;
        backdrop-filter: blur(8px);
        transition: border-color 0.3s;
    }
    .glass-card:hover { border-color: rgba(168,85,247,0.3); }

    /* ACTION CARD (Ya no es 3D, ahora es plana como las demás) */
    .action-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.25rem;
        backdrop-filter: blur(8px);
        transition: border-color 0.3s;
        padding: 1.5rem;
        position: relative;
    }
    .action-card:hover { border-color: rgba(168,85,247,0.3); }
    .action-card-glow, .action-card-scanline, .action-card-orb { display: none; }
    
    .action-card-inner {
        position: relative; z-index: 5;
        display: flex; flex-direction: column; gap: 1.25rem;
    }
    @media (min-width: 640px) { .action-card-inner { flex-direction: row; align-items: flex-start; } }
    .action-icon-box {
        width: 3.5rem; height: 3.5rem; border-radius: 1rem;
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; box-shadow: 0 0 20px rgba(168,85,247,0.4);
    }

    .alert-icon-box {
        width: 2.75rem; height: 2.75rem; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 0.1rem;
    }
    .alert-content { flex: 1; min-width: 0; }
    .alert-title {
        font-size: 0.875rem; font-weight: 800; color: white;
        margin-bottom: 0.2rem;
    }
    .alert-sub { font-size: 0.78rem; color: rgba(148,163,184,0.7); line-height: 1.5; }
    /* Cooldown progress bar inside alert */
    .cooldown-bar-wrap {
        margin-top: 0.625rem;
        background: rgba(0,0,0,0.35); border-radius: 9999px;
        height: 3px; overflow: hidden;
    }
    .cooldown-bar-fill {
        height: 100%; border-radius: 9999px;
        background: linear-gradient(90deg, #7c3aed, #ec4899);
        transition: width 1s linear;
    }

    /* === COOLDOWN TIMER === */
    .cooldown-timer {
        font-size: 1.5rem; font-weight: 900;
        font-variant-numeric: tabular-nums; letter-spacing: -0.03em;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
        flex-shrink: 0; align-self: center;
    }

    /* === METRICS BAR — GOD LEVEL === */
    .metrics-bar {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (min-width: 640px)  { .metrics-bar { grid-template-columns: repeat(2, 1fr) repeat(2, 1fr); } }
    @media (min-width: 1024px) { .metrics-bar { grid-template-columns: 1.8fr 1fr 1fr 1fr 1fr; } }

    .metric-card {
        background: rgba(255,255,255,0.025);
        border: 1px solid rgba(255,255,255,0.08);
        border-top: 2px solid var(--mc, #a855f7);
        border-radius: 1.25rem;
        padding: 1.25rem 1.5rem;
        position: relative; overflow: hidden;
        backdrop-filter: blur(12px);
        transition: border-color 0.3s, transform 0.2s, box-shadow 0.3s;
    }
    .metric-card:hover {
        border-color: var(--mc, #a855f7);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        transform: translateY(-3px);
    }
    .metric-card::before {
        display: none;
    }
    /* Featured metric (first card, wider) */
    .metric-featured {
        grid-row: span 1;
    }
    @media (min-width: 640px) { .metric-featured { grid-column: span 2; } }
    @media (min-width: 1024px) { .metric-featured { grid-column: span 1; } }
    .metric-featured .metric-num {
        font-size: 2.75rem;
    }
    .metric-featured-bar-bg {
        height: 5px; border-radius: 9999px;
        background: rgba(0,0,0,0.5); overflow: hidden;
        margin-top: 0.875rem; margin-bottom: 0.35rem;
    }
    .metric-featured-bar-fill {
        height: 100%; border-radius: 9999px;
        background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899);
        box-shadow: 0 0 10px rgba(168,85,247,0.5);
    }
    .metric-featured-sub {
        font-size: 0.65rem; color: rgba(148,163,184,0.4);
        display: flex; justify-content: space-between;
    }

    .metric-icon {
        width: 2.5rem; height: 2.5rem; border-radius: 0.875rem;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1rem;
    }
    .metric-num {
        font-size: 2rem; font-weight: 900; color: white;
        line-height: 1; margin-bottom: 0.25rem;
        transition: color 0.4s;
    }
    .metric-label {
        font-size: 0.68rem; font-weight: 600; color: rgba(148,163,184,0.55);
        text-transform: uppercase; letter-spacing: 0.1em;
    }
    .metric-context {
        font-size: 0.62rem; color: rgba(148,163,184,0.3);
        margin-top: 0.2rem;
    }

    /* === PLATFORM CHIPS === */
    .platform-chip {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 0.5rem; padding: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem; font-size: 0.75rem; font-weight: 700; color: white;
        transition: all 0.2s; text-align: center;
        width: 100px;
    }
    .platform-chip:hover {
        background: rgba(255,255,255,0.06); border-color: rgba(168,85,247,0.3);
        transform: translateY(-2px);
    }
    .platform-chip img {
        width: 2rem !important; height: 2rem !important; margin-bottom: 0.25rem;
    }
    .platform-dot {
        width: 0.5rem; height: 0.5rem; border-radius: 50%;
    }

    /* === WARRANTY ROWS === */
    .warranty-row {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.875rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .warranty-row:last-child { border-bottom: none; padding-bottom: 0; }
    .warranty-icon {
        width: 2.25rem; height: 2.25rem; border-radius: 0.625rem;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .warranty-info { flex: 1; min-width: 0; }
    .warranty-platform { font-size: 0.85rem; font-weight: 700; color: white; }
    .warranty-type { font-size: 0.72rem; color: rgba(148,163,184,0.55); margin-top: 0.1rem; }
    .warranty-badge {
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; padding: 0.2rem 0.6rem;
        border-radius: 9999px; border: 1px solid; flex-shrink: 0;
    }
    .wb-pending  { color: #fbbf24; background: rgba(251,191,36,0.07); border-color: rgba(251,191,36,0.2); }
    .wb-approved { color: #34d399; background: rgba(52,211,153,0.07); border-color: rgba(52,211,153,0.2); }
    .wb-rejected { color: #f87171; background: rgba(239,68,68,0.07); border-color: rgba(239,68,68,0.2); }
    .wb-default  { color: #a855f7; background: rgba(168,85,247,0.07); border-color: rgba(168,85,247,0.2); }

    /* === SECTION TITLES === */
    .section-title {
        font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .section-title::after {
        content: ''; flex: 1; height: 1px;
        background: linear-gradient(90deg, rgba(168,85,247,0.3), transparent);
        -webkit-text-fill-color: initial; background-clip: initial;
    }

    /* === MAIN ACTION CARD — GOD LEVEL === */
    .action-card {
        background: linear-gradient(145deg, rgba(14,8,35,0.98) 0%, rgba(8,4,20,1) 100%);
        background-image:
            linear-gradient(145deg, rgba(14,8,35,0.95) 0%, rgba(8,4,20,1) 100%),
            radial-gradient(circle at 50% 0%, rgba(168,85,247,0.1) 0%, transparent 50%);
        border: 1px solid rgba(168,85,247,0.2);
        border-radius: 1.75rem;
        padding: 2.5rem;
        position: relative; overflow: hidden;
        transition: border-color 0.4s, box-shadow 0.4s, transform 0.3s;
    }
    .action-card:hover {
        border-color: rgba(168,85,247,0.5);
        box-shadow: 0 0 0 1px rgba(168,85,247,0.1), 0 20px 40px rgba(0,0,0,0.4), 0 0 60px rgba(168,85,247,0.15);
        transform: translateY(-4px);
    }
    .action-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, transparent, #7c3aed, #a855f7, #ec4899, transparent);
    }
    .action-card-orb {
        position: absolute; top: -60px; right: -60px;
        width: 250px; height: 250px;
        background: radial-gradient(circle, rgba(168,85,247,0.15), transparent 70%);
        border-radius: 50%; pointer-events: none;
    }
    .action-icon-box {
        width: 4.5rem; height: 4.5rem;
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        border-radius: 1.25rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 25px rgba(124,58,237,0.5), inset 0 2px 4px rgba(255,255,255,0.3);
        flex-shrink: 0;
        position: relative;
    }
    .action-icon-box::after {
        content:''; position:absolute; inset:-4px; border-radius:1.5rem;
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        z-index:-1; filter:blur(10px); opacity:0.6;
    }
    
    .action-card-inner {
        position: relative; z-index: 1;
        display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1.5rem;
    }
    @media (min-width: 640px) {
        .action-card-inner { flex-direction: row; align-items: flex-start; text-align: left; }
    }

    /* === BIG BUTTON — GOD LEVEL === */
    .btn-primary {
        display: inline-flex; align-items: center; gap: 0.875rem;
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        border: none; border-radius: 1rem;
        color: white; font-weight: 800; font-size: 1rem; letter-spacing:0.02em;
        padding: 1rem 2.25rem; text-decoration: none;
        box-shadow: 0 8px 25px rgba(168,85,247,0.4), 0 0 0 1px rgba(168,85,247,0.2) inset;
        transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
        position: relative; overflow: hidden;
    }
    .btn-primary::before {
        content:''; position:absolute; inset:0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transform: translateX(-100%) skewX(-15deg);
        transition: transform 0.6s ease;
    }
    .btn-primary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 30px rgba(168,85,247,0.5), 0 0 0 1px rgba(255,255,255,0.2) inset;
    }
    .btn-primary:hover::before { transform: translateX(100%) skewX(-15deg); }
    
    .btn-disabled {
        display: inline-flex; align-items: center; gap: 0.75rem;
        background: rgba(255,255,255,0.03);
        border: 1px dashed rgba(255,255,255,0.15); border-radius: 1rem;
        color: rgba(148,163,184,0.5); font-weight: 700; font-size: 0.95rem;
        padding: 0.9rem 2rem; cursor: not-allowed;
    }

    /* === DONUT CHART === */
    .donut-wrap {
        display: flex; flex-direction: column; align-items: center; gap: 1.5rem;
        margin-bottom: 1.25rem; text-align: center;
    }
    @media (min-width: 640px) {
        .donut-wrap { flex-direction: row; text-align: left; gap: 1.75rem; }
    }
    .donut-svg { flex-shrink: 0; filter: drop-shadow(0 0 8px rgba(168,85,247,0.3)); }
    .donut-center-text { text-anchor: middle; dominant-baseline: middle; }
    .donut-info { flex: 1; width: 100%; }
    .donut-stat-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.625rem 0; border-bottom: 1px solid rgba(255,255,255,0.04);
        font-size: 0.85rem;
    }
    .donut-stat-row:last-child { border-bottom: none; }
    .donut-stat-label { color: rgba(148,163,184,0.65); font-weight:500; }
    .donut-stat-val { font-weight: 800; color: white; }

    /* === BOTTOM GRID === */
    .dash-grid {
        display: grid; grid-template-columns: 1fr; gap: 1.5rem;
    }
    @media (min-width: 1024px) { .dash-grid { grid-template-columns: 1fr 1fr; } }

    /* === SUPPORT BUTTONS — GOD LEVEL === */
    .btn-support {
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem 1.25rem; border-radius: 1rem;
        text-decoration: none; font-weight: 700; font-size: 0.9rem;
        transition: all 0.3s cubic-bezier(0.16,1,0.3,1); border: 1px solid transparent; width: 100%;
        margin-bottom: 0.75rem; position: relative; overflow: hidden;
    }
    .btn-tg { background: rgba(38,165,228,0.05); color: #38bdf8; border-color: rgba(38,165,228,0.15); }
    .btn-tg:hover { background: rgba(38,165,228,0.1); border-color: rgba(38,165,228,0.4); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(38,165,228,0.15); }
    .btn-wa { background: rgba(37,211,102,0.05); color: #34d399; border-color: rgba(37,211,102,0.15); }
    .btn-wa:hover { background: rgba(37,211,102,0.1); border-color: rgba(37,211,102,0.4); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,211,102,0.15); }
    .btn-support-label { flex: 1; text-align: left; }

    /* === QUICK LINKS — GOD LEVEL === */
    .quick-link {
        display: flex; align-items: center; gap: 1.25rem;
        padding: 1rem 1.25rem; border-radius: 1rem;
        border: 1px solid rgba(168,85,247,0.1);
        background: rgba(255,255,255,0.02);
        text-decoration: none; transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
        color: rgba(148,163,184,0.9);
        margin-bottom: 0.75rem;
    }
    .quick-link:hover {
        background: linear-gradient(90deg, rgba(168,85,247,0.08), rgba(168,85,247,0.02));
        border-color: rgba(168,85,247,0.3);
        color: white; transform: translateX(6px);
        box-shadow: 0 4px 15px rgba(168,85,247,0.05);
    }
    .quick-link-icon {
        width: 2.75rem; height: 2.75rem; border-radius: 0.75rem;
        background: rgba(168,85,247,0.1);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        transition: transform 0.3s;
    }
    .quick-link:hover .quick-link-icon { transform: scale(1.1) rotate(5deg); background: rgba(168,85,247,0.2); }
    .quick-link-text { font-size: 0.95rem; font-weight: 700; margin-bottom: 0.1rem; }
    .quick-link-sub { font-size: 0.75rem; color: rgba(148,163,184,0.5); }

    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-in { animation: fade-in-up 0.4s ease both; }
    .delay-1 { animation-delay: 0.08s; }
    .delay-2 { animation-delay: 0.16s; }
    .delay-3 { animation-delay: 0.24s; }
    .delay-4 { animation-delay: 0.32s; }

    /* ═══ NIVEL 3: EFECTOS PREMIUM ═══ */

    /* Neon ring pulsante en el avatar */
    @keyframes neon-ring {
        0%   { box-shadow: 0 0 0 0 rgba(168,85,247,0.5), 0 0 30px rgba(168,85,247,0.3); }
        50%  { box-shadow: 0 0 0 8px rgba(168,85,247,0), 0 0 40px rgba(236,72,153,0.4); }
        100% { box-shadow: 0 0 0 0 rgba(168,85,247,0), 0 0 30px rgba(168,85,247,0.3); }
    }
    .hero-avatar { animation: neon-ring 3s ease-in-out infinite; }

    /* Reloj en vivo */
    .live-clock {
        font-size: 1.25rem; font-weight: 900; color: white;
        font-variant-numeric: tabular-nums; letter-spacing: -0.04em;
        line-height: 1;
        background: linear-gradient(135deg, #f1f5f9, #a855f7);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .live-clock-label {
        font-size: 0.62rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.12em; color: rgba(148,163,184,0.45); margin-top: 0.2rem;
    }
    .live-date {
        font-size: 0.72rem; color: rgba(148,163,184,0.5); font-weight: 500;
        margin-top: 0.1rem;
    }

    /* Borde rotativo en action card */
    @keyframes border-spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .action-card-glow {
        position: absolute; inset: -1px; border-radius: 1.5rem;
        background: conic-gradient(from 0deg, transparent 0%, #7c3aed 25%, #a855f7 50%, #ec4899 75%, transparent 100%);
        z-index: 0; animation: border-spin 4s linear infinite;
        opacity: 0; transition: opacity 0.4s;
    }
    .action-card:hover .action-card-glow { opacity: 0.6; }
    .action-card-inner { position: relative; z-index: 1; }

    /* Shimmer / scanline effect en action card */
    @keyframes scanline {
        0%   { transform: translateY(-100%); }
        100% { transform: translateY(400%); }
    }
    .action-card-scanline {
        position: absolute; left: 0; right: 0; height: 30%;
        background: linear-gradient(180deg, transparent, rgba(168,85,247,0.04), transparent);
        pointer-events: none; z-index: 2;
        animation: scanline 4s linear infinite;
    }

    /* Counters: el número sale con un brillo al terminar */
    @keyframes counter-pop {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.12); filter: brightness(1.3); }
        100% { transform: scale(1); }
    }
    .counter-done { animation: counter-pop 0.3s ease; }

    /* Glassmorphism shimmer en glass-card al hover */
    .glass-card {
        position: relative; overflow: hidden;
    }
    .glass-card::after {
        content: '';
        position: absolute; top: 0; left: -100%;
        width: 60%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03), transparent);
        transform: skewX(-20deg);
        transition: left 0.5s ease;
        pointer-events: none;
    }
    .glass-card:hover::after { left: 150%; }

    /* Badge "EN VIVO" parpadeante */
    @keyframes blink-live {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.3; }
    }
    .live-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        font-size: 0.6rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.1em; color: #34d399;
        background: rgba(52,211,153,0.08); border: 1px solid rgba(52,211,153,0.2);
        padding: 0.2rem 0.6rem; border-radius: 9999px;
    }
    .live-dot {
        width: 0.4rem; height: 0.4rem; border-radius: 50%;
        background: #34d399; box-shadow: 0 0 6px #34d399;
        animation: blink-live 1.2s ease-in-out infinite;
    }

    /* Metric card número animado */
    .metric-num { transition: color 0.3s; }
</style>
@endsection

@section('content')

@php
    $maxQueries   = $client->max_queries_per_day ?? 100;
    $usedQueries  = $client->query_count;
    $available    = max(0, $maxQueries - $usedQueries);
    $percentage   = min(100, ($usedQueries / max(1, $maxQueries)) * 100);
    $canQuery     = $client->canMakeQuery();
    $accessMode   = $client->access_mode ?? 'all';
    $daysSinceQuery = $client->last_query_at
        ? (int) $client->last_query_at->diffInDays(now())
        : null;

    // Cooldown: tiempo restante en segundos
    $cooldownMinutes = (int) \App\Models\Setting::get(\App\Models\Setting::KEY_QUERY_COOLDOWN_MINUTES, 30);
    $cooldownSeconds = 0;
    if (!$canQuery && $client->last_query_at) {
        $cooldownSeconds = max(0, $client->last_query_at->addMinutes($cooldownMinutes)->diffInSeconds(now()));
    }

    // Donut SVG values
    $radius = 52; $circ = 2 * M_PI * $radius;
    $fill = $circ - ($percentage / 100) * $circ;

    // Greeting by time
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Buenos días' : ($hour < 19 ? 'Buenas tardes' : 'Buenas noches');

    $initial = strtoupper(mb_substr($client->name, 0, 1));
@endphp

{{-- ═══ HERO ═══ --}}
<div class="dash-hero animate-in">
    <div class="dash-hero-bottom-glow"></div>
    <div class="dash-hero-orb-tl"></div>
    <div class="dash-hero-orb-br"></div>

    {{-- Left block: Identity --}}
    <div class="hero-left">
        <div class="hero-avatar-wrap">
            <div class="hero-avatar" style="overflow:hidden;">
                @if($client->avatar)
                    <img src="{{ asset($client->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="Avatar">
                @else
                    {{ $initial }}
                @endif
            </div>
            @if($client->is_active)
            <div class="hero-avatar-online"></div>
            @endif
        </div>
        <div class="hero-identity">
            <p class="hero-greeting" id="dynamicGreeting">
                @if($hour < 12) ☀️
                @elseif($hour < 19) 🌤️
                @else 🌙
                @endif
                {{ $greeting }}, bienvenido
            </p>
            <h1 class="hero-name">{{ $client->name }}</h1>
            <p class="hero-email">{{ $client->email }}</p>
            <div class="hero-badges">
                @if($client->is_active)
                    <span class="status-active">
                        <span class="status-dot green"></span> Activa
                    </span>
                @else
                    <span class="status-inactive">
                        <span class="status-dot red"></span> Inactiva
                    </span>
                @endif
                <span class="badge-mode">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        @if($accessMode === 'all')
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        @endif
                    </svg>
                    {{ $accessMode === 'all' ? 'Total' : 'Selectivo' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Divider --}}
    <div class="hero-divider"></div>

    {{-- Right block: Usage & Clock --}}
    <div class="hero-right">
        @if($daysSinceQuery !== null && $daysSinceQuery >= 3)
        <div class="hero-usage">
            <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(168,85,247,0.7);">Sin actividad</p>
            <p style="font-size:1.5rem;font-weight:900;color:white;line-height:1;margin-top:0.2rem;">{{ $daysSinceQuery }} <span style="font-size:0.7rem;color:rgba(148,163,184,0.5);">días</span></p>
        </div>
        @else
        <div class="hero-usage">
            <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(168,85,247,0.7);display:flex;justify-content:space-between;">Uso Diario <span>{{ round($percentage) }}%</span></p>
            <div class="hero-usage-bar-bg">
                <div class="hero-usage-bar-fill" style="width: {{ $percentage }}%;"></div>
            </div>
            <p style="font-size:0.65rem;color:rgba(148,163,184,0.5);">{{ $available }} disp. de {{ $maxQueries }}</p>
        </div>
        @endif
        
        <div class="hero-clock-wrap">
            <div class="live-badge" style="margin-bottom:0.25rem;">
                <span class="live-dot"></span> EN VIVO
            </div>
            <div class="live-clock" id="liveClock">{{ now()->format('H:i:s') }}</div>
            <div class="live-date" id="liveDate">
                @php
                    $d = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'][now()->dayOfWeek];
                    $m = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][now()->month - 1];
                @endphp
                {{ $d }} {{ now()->day }} {{ $m }}
            </div>
        </div>
    </div>
</div>

{{-- ═══ ALERTAS CONTEXTUALES ═══ --}}

{{-- Alerta: límite casi alcanzado (>= 80%) --}}
@if($percentage >= 80 && $percentage < 100)
<div class="alert-banner alert-warning animate-in delay-1">
    <div class="alert-icon-box" style="background:rgba(251,191,36,0.1);">
        <svg width="20" height="20" fill="none" stroke="#fbbf24" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <div class="alert-content">
        <p class="alert-title">Límite casi alcanzado</p>
        <p class="alert-sub">Has usado el <strong>{{ round($percentage) }}%</strong> de tus consultas diarias. Te quedan <strong>{{ $available }}</strong> disponibles hoy.</p>
    </div>
</div>
@endif

{{-- Alerta: límite al 100% --}}
@if($percentage >= 100)
<div class="alert-banner alert-cooldown animate-in delay-1">
    <div class="alert-icon-box" style="background:rgba(236,72,153,0.1);">
        <svg width="20" height="20" fill="none" stroke="#ec4899" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
    </div>
    <div class="alert-content">
        <p class="alert-title">Límite diario alcanzado</p>
        <p class="alert-sub">Has agotado tus <strong>{{ $maxQueries }}</strong> consultas de hoy. El contador se reiniciará mañana automáticamente.</p>
    </div>
</div>
@endif

{{-- Alerta: cooldown activo --}}
@if(!$canQuery && $percentage < 100)
<div class="alert-banner alert-cooldown animate-in delay-1">
    <div class="alert-icon-box" style="background:rgba(236,72,153,0.1);">
        <svg width="20" height="20" fill="none" stroke="#ec4899" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="alert-content">
        <p class="alert-title">Espera entre consultas</p>
        <p class="alert-sub">Debes esperar <strong>{{ $cooldownMinutes }} min</strong> entre cada consulta.</p>
        <div class="cooldown-bar-wrap">
            <div class="cooldown-bar-fill" id="cooldownBarFill" style="width:100%;"></div>
        </div>
    </div>
    <div class="cooldown-timer" id="cooldownTimer">--:--</div>
</div>
@endif

{{-- ═══ METRICS BAR ═══ --}}
<div class="metrics-bar animate-in delay-1">
    <div class="metric-card metric-featured" style="--mc:#a855f7;">
        <div class="metric-icon" style="background:rgba(168,85,247,0.12);">
            <svg width="24" height="24" fill="none" stroke="#a855f7" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <div class="metric-num" id="cnt-used" data-target="{{ $usedQueries }}">0</div>
        <div class="metric-label">Consultas Hoy</div>
        <div class="metric-featured-bar-bg">
            <div class="metric-featured-bar-fill" style="width: {{ $percentage }}%;"></div>
        </div>
        <div class="metric-featured-sub">
            <span>Uso Diario</span>
            <span>{{ round($percentage) }}%</span>
        </div>
    </div>

    <div class="metric-card" style="--mc:#ec4899;">
        <div class="metric-icon" style="background:rgba(236,72,153,0.12);">
            <svg width="20" height="20" fill="none" stroke="#ec4899" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="metric-num" id="cnt-max" data-target="{{ $maxQueries }}">0</div>
        <div class="metric-label">Límite Diario</div>
        <div class="metric-context">Máx. de plan</div>
    </div>

    <div class="metric-card" style="--mc:{{ $available == 0 ? '#f87171' : ($available < ($maxQueries*0.2) ? '#fbbf24' : '#34d399') }};">
        <div class="metric-icon" style="background:rgba({{ $available == 0 ? '248,113,113' : ($available < ($maxQueries*0.2) ? '251,191,36' : '52,211,153') }},0.12);">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:{{ $available == 0 ? '#f87171' : ($available < ($maxQueries*0.2) ? '#fbbf24' : '#34d399') }}"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="metric-num" id="cnt-avail" data-target="{{ $available }}" style="color:{{ $available == 0 ? '#f87171' : ($available < ($maxQueries*0.2) ? '#fbbf24' : '#34d399') }}">0</div>
        <div class="metric-label">Disponibles</div>
        <div class="metric-context">Consultas restantes</div>
    </div>

    <div class="metric-card" style="--mc:#38bdf8;">
        <div class="metric-icon" style="background:rgba(56,189,248,0.12);">
            <svg width="20" height="20" fill="none" stroke="#38bdf8" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="metric-num" style="font-size:{{ $client->last_query_at ? '1.75rem' : '2rem' }};line-height:1.15;margin-bottom:0.25rem;">
            @if($client->last_query_at) {{ $client->last_query_at->format('H:i') }} @else — @endif
        </div>
        <div class="metric-label">Última</div>
        <div class="metric-context">@if($client->last_query_at) {{ $client->last_query_at->diffForHumans() }} @else Sin registro @endif</div>
    </div>

    <div class="metric-card" style="--mc:#a78bfa;">
        <div class="metric-icon" style="background:rgba(167,139,250,0.12);">
            <svg width="20" height="20" fill="none" stroke="#a78bfa" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div class="metric-num" id="cnt-emails" data-target="{{ $allowedEmailCount }}">0</div>
        <div class="metric-label">Correos</div>
        <div class="metric-context">Permitidos</div>
    </div>
</div>

{{-- ═══ MAIN ACTION & PLATFORMS GRID ═══ --}}
<div class="dash-grid animate-in delay-2" style="margin-bottom:1.5rem;">
    
    {{-- Acción Principal --}}
    <div>
        <div class="section-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            Acción Principal
        </div>

        <div class="action-card">
            <div class="action-card-inner">
                <div class="action-icon-box">
                    <svg width="26" height="26" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div style="flex:1;">
                    <h2 style="font-size:1.2rem;font-weight:900;color:white;margin-bottom:0.4rem;">Extraer Código</h2>
                    <p style="font-size:0.875rem;color:rgba(148,163,184,0.75);line-height:1.6;margin-bottom:1.5rem;">
                        Nuestro motor IMAP buscará automáticamente el código de acceso más reciente.
                    </p>

                    @if($client->is_active && $canQuery && $available > 0)
                        <a href="{{ route('client.query') }}" class="btn-primary">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Iniciar Búsqueda
                        </a>
                    @elseif(!$canQuery)
                        <span class="btn-disabled">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            En espera de cooldown…
                        </span>
                    @else
                        <span class="btn-disabled">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Límite alcanzado hoy
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Plataformas Asignadas --}}
    @if($clientPlatforms->isNotEmpty())
    <div>
        <div class="section-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Plataformas Asignadas
        </div>
        <div class="glass-card" style="padding:1.5rem; height: calc(100% - 2.5rem);">
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
                @foreach($clientPlatforms as $platform)
                <div class="platform-chip">
                    @if($platform->logo)
                        <img src="{{ asset(str_starts_with($platform->logo, 'platforms_logos') ? $platform->logo : 'storage/'.$platform->logo) }}" alt="{{ $platform->name }}" style="width:2rem;height:2rem;object-fit:contain;border-radius:0.25rem;margin-bottom:0.25rem;">
                    @else
                        <span class="platform-dot" style="background:{{ $platform->color ?? '#a855f7' }};"></span>
                    @endif
                    <span>{{ $platform->name }}</span>
                </div>
                @endforeach
            </div>
            <p style="font-size:0.72rem;color:rgba(148,163,184,0.4);margin-top:1rem;">
                Tienes acceso a <strong style="color:rgba(168,85,247,0.8);">{{ $clientPlatforms->count() }}</strong> plataforma(s) activa(s) en tu plan.
            </p>
        </div>
    </div>
    @endif

</div>

{{-- ═══ BOTTOM GRID ═══ --}}
<div class="dash-grid animate-in delay-3">

    {{-- Donut + Estadísticas del Plan --}}
    <div>
        <div class="section-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Uso del Plan
        </div>
        <div class="glass-card" style="padding:1.75rem;">
            <div class="donut-wrap">
                {{-- Donut SVG --}}
                <svg class="donut-svg" width="130" height="130" viewBox="0 0 130 130">
                    <defs>
                        <linearGradient id="donutGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#7c3aed"/>
                            <stop offset="100%" style="stop-color:#ec4899"/>
                        </linearGradient>
                    </defs>
                    {{-- Track --}}
                    <circle cx="65" cy="65" r="{{ $radius }}"
                        fill="none" stroke="rgba(255,255,255,0.05)"
                        stroke-width="12"/>
                    {{-- Progress --}}
                    <circle cx="65" cy="65" r="{{ $radius }}"
                        fill="none" stroke="url(#donutGrad)"
                        stroke-width="12"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $circ }}"
                        stroke-dashoffset="{{ $fill }}"
                        transform="rotate(-90 65 65)"
                        style="filter:drop-shadow(0 0 8px rgba(168,85,247,0.5));transition:stroke-dashoffset 1s ease;"/>
                    {{-- Center text --}}
                    <text x="65" y="60" class="donut-center-text"
                        font-size="20" font-weight="900" fill="white"
                        font-family="Inter,sans-serif">{{ round($percentage) }}%</text>
                    <text x="65" y="78" class="donut-center-text"
                        font-size="9.5" font-weight="600" fill="rgba(148,163,184,0.5)"
                        font-family="Inter,sans-serif" text-transform="uppercase">USADO</text>
                </svg>

                <div class="donut-info">
                    <div class="donut-stat-row">
                        <span class="donut-stat-label">Usadas hoy</span>
                        <span class="donut-stat-val">{{ $usedQueries }}</span>
                    </div>
                    <div class="donut-stat-row">
                        <span class="donut-stat-label">Límite diario</span>
                        <span class="donut-stat-val">{{ $maxQueries }}</span>
                    </div>
                    <div class="donut-stat-row">
                        <span class="donut-stat-label">Disponibles</span>
                        <span class="donut-stat-val" style="{{ $available == 0 ? 'color:#f87171' : 'color:#34d399' }}">{{ $available }}</span>
                    </div>
                    <div class="donut-stat-row">
                        <span class="donut-stat-label">Última consulta</span>
                        <span class="donut-stat-val" style="font-size:0.78rem;">
                            @if($client->last_query_at) {{ $client->last_query_at->diffForHumans() }} @else Sin actividad @endif
                        </span>
                    </div>
                    <div class="donut-stat-row">
                        <span class="donut-stat-label">Modo de acceso</span>
                        <span class="donut-stat-val" style="font-size:0.78rem;color:#a855f7;">{{ $accessMode === 'all' ? 'Total' : 'Selectivo' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel derecho --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem;">

        {{-- Accesos Rápidos --}}
        <div>
            <div class="section-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Accesos Rápidos
            </div>
            <div class="glass-card" style="padding:1.25rem;">
                <a href="{{ route('client.query') }}" class="quick-link">
                    <div class="quick-link-icon" style="background:rgba(168,85,247,0.12);">
                        <svg width="18" height="18" fill="none" stroke="#a855f7" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <div class="quick-link-text">Consultar Código</div>
                        <div class="quick-link-sub">Extraer código IMAP en tiempo real</div>
                    </div>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.3;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('client.warranties.index') }}" class="quick-link">
                    <div class="quick-link-icon" style="background:rgba(52,211,153,0.12);">
                        <svg width="18" height="18" fill="none" stroke="#34d399" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="quick-link-text">Mis Garantías</div>
                        <div class="quick-link-sub">Gestionar reportes y reclamos</div>
                    </div>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.3;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('client.profile') }}" class="quick-link" style="margin-bottom:0;">
                    <div class="quick-link-icon" style="background:rgba(236,72,153,0.12);">
                        <svg width="18" height="18" fill="none" stroke="#ec4899" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="quick-link-text">Mi Perfil</div>
                        <div class="quick-link-sub">Cambiar contraseña y datos</div>
                    </div>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.3;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Soporte --}}
        @if($contactTelegram || $contactWhatsapp)
        <div>
            <div class="section-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Soporte Técnico
            </div>
            <div class="glass-card" style="padding:1.25rem;">
                <p style="font-size:0.82rem;color:rgba(148,163,184,0.65);line-height:1.6;margin-bottom:1rem;">¿Necesitas ayuda? Contáctanos por nuestros canales oficiales.</p>
                @if($contactTelegram)
                <a href="{{ $contactTelegram }}" target="_blank" class="btn-support btn-tg">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.696.064-1.225-.46-1.901-.903-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    <span class="btn-support-label">Contactar por Telegram</span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                @endif
                @if($contactWhatsapp)
                <a href="{{ $contactWhatsapp }}?text={{ urlencode($whatsappMessage) }}" target="_blank" class="btn-support btn-wa" style="margin-bottom:0;">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span class="btn-support-label">Contactar por WhatsApp</span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>



{{-- ═══ ÚLTIMAS GARANTÍAS ═══ --}}
@if($recentWarranties->isNotEmpty())
<div style="margin-top:1.5rem;" class="animate-in delay-4">
    <div class="section-title">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Últimas Garantías
    </div>
    <div class="glass-card" style="padding:1.5rem;">
        @foreach($recentWarranties as $warranty)
        @php
            $wStatusClass = match($warranty->status) {
                'pending'  => 'wb-pending',
                'approved' => 'wb-approved',
                'rejected' => 'wb-rejected',
                default    => 'wb-default',
            };
            $wStatusLabel = match($warranty->status) {
                'pending'  => 'Pendiente',
                'approved' => 'Aprobada',
                'rejected' => 'Rechazada',
                default    => ucfirst($warranty->status),
            };
            $wType = match($warranty->type ?? '') {
                'replacement' => 'Reemplazo de correo',
                'refund'      => 'Reembolso',
                'complaint'   => 'Queja / Reclamo',
                default       => 'Solicitud de garantía',
            };
        @endphp
        <div class="warranty-row">
            <div class="warranty-icon" style="background:rgba(168,85,247,0.1);">
                <svg width="18" height="18" fill="none" stroke="#a855f7" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="warranty-info">
                <div class="warranty-platform">{{ $warranty->platform?->name ?? 'Sin plataforma' }}</div>
                <div class="warranty-type">{{ $wType }} &middot; {{ $warranty->created_at->diffForHumans() }}</div>
            </div>
            <span class="warranty-badge {{ $wStatusClass }}">{{ $wStatusLabel }}</span>
        </div>
        @endforeach
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.05);">
            <a href="{{ route('client.warranties.index') }}" style="font-size:0.8rem;font-weight:600;color:#a855f7;text-decoration:none;display:inline-flex;align-items:center;gap:0.375rem;">
                Ver todas mis garantías
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
@if(!$canQuery && $cooldownSeconds > 0)
<script>
    (function() {
        let remaining = {{ $cooldownSeconds }};
        const el = document.getElementById('cooldownTimer');
        if (!el) return;
        function fmt(s) {
            const m = Math.floor(s / 60);
            const sec = s % 60;
            return String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
        }
        el.textContent = fmt(remaining);
        const iv = setInterval(function() {
            remaining--;
            if (remaining <= 0) {
                clearInterval(iv);
                location.reload();
            } else {
                el.textContent = fmt(remaining);
            }
        }, 1000);
    })();
</script>
@endif

{{-- ═══ NIVEL 3: JS PREMIUM ═══ --}}
<script>
(function() {
    // ─── 1. RELOJ EN VIVO ───────────────────────────────────────────
    const clockEl = document.getElementById('liveClock');
    const dateEl  = document.getElementById('liveDate');
    const days    = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    const months  = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        const s = String(now.getSeconds()).padStart(2,'0');
        if (clockEl) clockEl.textContent = h + ':' + m + ':' + s;
        if (dateEl) dateEl.textContent =
            days[now.getDay()] + ' ' + now.getDate() + ' ' + months[now.getMonth()];

        const greetingEl = document.getElementById('dynamicGreeting');
        if (greetingEl) {
            let hour = now.getHours();
            let gText = '';
            let gIcon = '';
            if (hour < 12) { gText = 'Buenos días'; gIcon = '☀️'; }
            else if (hour < 19) { gText = 'Buenas tardes'; gIcon = '🌤️'; }
            else { gText = 'Buenas noches'; gIcon = '🌙'; }
            greetingEl.innerHTML = gIcon + ' ' + gText + ', bienvenido';
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ─── 2. CONTADORES ANIMADOS ──────────────────────────────────────
    function animateCounter(el, target, duration) {
        if (!el) return;
        const start    = performance.now();
        const startVal = 0;
        function step(now) {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / duration, 1);
            // easeOutExpo
            const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            el.textContent = Math.round(startVal + (target - startVal) * ease);
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target;
                el.classList.add('counter-done');
                setTimeout(() => el.classList.remove('counter-done'), 400);
            }
        }
        requestAnimationFrame(step);
    }

    // Lanzar contadores con pequeño delay stagger
    const counters = [
        { id: 'cnt-used',   delay: 200 },
        { id: 'cnt-max',    delay: 350 },
        { id: 'cnt-avail',  delay: 500 },
        { id: 'cnt-emails', delay: 650 },
    ];
    counters.forEach(function(c) {
        const el = document.getElementById(c.id);
        if (!el) return;
        const target = parseInt(el.dataset.target, 10) || 0;
        setTimeout(function() { animateCounter(el, target, 900); }, c.delay);
    });

    // ─── 3. CANVAS PARTÍCULAS EN EL HERO ────────────────────────────
    const hero = document.querySelector('.dash-hero');
    if (hero) {
        const canvas = document.createElement('canvas');
        canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:0;border-radius:1.5rem;';
        hero.style.position = 'relative';
        hero.prepend(canvas);

        const ctx = canvas.getContext('2d');
        let particles = [];
        const PARTICLE_COUNT = 28;

        function resize() {
            canvas.width  = hero.offsetWidth;
            canvas.height = hero.offsetHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        for (let i = 0; i < PARTICLE_COUNT; i++) {
            particles.push({
                x:  Math.random() * canvas.width,
                y:  Math.random() * canvas.height,
                r:  Math.random() * 1.5 + 0.5,
                dx: (Math.random() - 0.5) * 0.3,
                dy: (Math.random() - 0.5) * 0.3,
                o:  Math.random() * 0.4 + 0.1,
            });
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(function(p) {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(168,85,247,' + p.o + ')';
                ctx.fill();
                p.x += p.dx;
                p.y += p.dy;
                if (p.x < 0 || p.x > canvas.width)  p.dx *= -1;
                if (p.y < 0 || p.y > canvas.height)  p.dy *= -1;
            });
            requestAnimationFrame(drawParticles);
        }
        drawParticles();
    }

    // ─── 4. GLOW MOUSE TRACKING EN METRIC CARDS ─────────────────────
    document.querySelectorAll('.metric-card').forEach(function(card) {
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width  * 100).toFixed(1);
            const y = ((e.clientY - rect.top)  / rect.height * 100).toFixed(1);
            card.style.background =
                'radial-gradient(circle at ' + x + '% ' + y + '%, rgba(168,85,247,0.1), rgba(14,8,35,0.98) 60%)';
        });
        card.addEventListener('mouseleave', function() {
            card.style.background = '';
        });
    });

})();
</script>
@endpush
