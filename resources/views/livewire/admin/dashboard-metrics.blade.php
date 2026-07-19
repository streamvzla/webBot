<style>
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ADMIN DASHBOARD — UI-* SYSTEM (GOD LEVEL v6.0)
     Paleta: BG #050510 | #7c3aed | #a855f7 | #ec4899
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}

/* â”€â”€ HERO â”€â”€ */
.ui-dash-hero {
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.08);
    border-top: 2px solid rgba(168,85,247,0.5);
    border-radius: 1.5rem; padding: 1.5rem 1.25rem;
    backdrop-filter: blur(12px); transition: border-color 0.3s;
    display: grid; grid-template-columns: 1fr; gap: 1.5rem; align-items: center;
    position: relative; overflow: hidden;
}
.ui-dash-hero::before {
    content: ''; position: absolute; top: -80px; right: -80px;
    width: 300px; height: 300px; border-radius: 50%;
    background: radial-gradient(circle, rgba(168,85,247,0.08), transparent 70%);
    pointer-events: none;
}
@media (min-width: 640px) { .ui-dash-hero { padding: 2rem 2.5rem; } }
@media (min-width: 900px) { .ui-dash-hero { grid-template-columns: 1fr 1px auto; gap: 2.5rem; } }
.ui-dash-hero:hover { border-top-color: rgba(168,85,247,0.9); }

.ui-hero-left { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem; position: relative; z-index: 1; }
@media (min-width: 640px) { .ui-hero-left { flex-direction: row; text-align: left; gap: 1.5rem; } }

.ui-hero-avatar-wrap { position: relative; flex-shrink: 0; width: 5rem; height: 5rem; border-radius: 50%; background: linear-gradient(135deg, rgba(168,85,247,0.5), rgba(236,72,153,0.3)); padding: 4px; z-index: 2; }
@media (min-width: 640px) { .ui-hero-avatar-wrap { width: 6rem; height: 6rem; } }
.ui-hero-avatar {
    width: 100%; height: 100%; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900; color: white; font-size: 2.2rem;
    background: linear-gradient(135deg,var(--ui-violet),var(--ui-pink));
    animation: neon-ring 3s ease-in-out infinite;
}
@keyframes neon-ring {
    0%   { box-shadow: 0 0 0 0 rgba(168,85,247,0.6), 0 0 30px rgba(168,85,247,0.3); }
    50%  { box-shadow: 0 0 0 10px rgba(168,85,247,0), 0 0 50px rgba(236,72,153,0.5); }
    100% { box-shadow: 0 0 0 0 rgba(168,85,247,0), 0 0 30px rgba(168,85,247,0.3); }
}

.ui-hero-identity { flex: 1; min-width: 0; width: 100%; }
.ui-hero-greeting { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: rgba(168,85,247,0.8); margin-bottom: 0.35rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
@media (min-width: 640px) { .ui-hero-greeting { justify-content: flex-start; } }
.ui-hero-name { font-size: 1.5rem; font-weight: 900; line-height: 1.15; margin-bottom: 0.2rem; background: linear-gradient(135deg, #ffffff 0%, rgba(196,181,253,0.85) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
@media (min-width: 640px) { .ui-hero-name { font-size: 2.2rem; line-height: 1.05; } }
.ui-hero-email { font-size: 0.78rem; color: rgba(148,163,184,0.4); margin-bottom: 0.75rem; word-break: break-all; }
.ui-hero-badges { display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap; }
@media (min-width: 640px) { .ui-hero-badges { justify-content: flex-start; } }

.ui-hero-divider { display: none; background: linear-gradient(180deg, transparent, rgba(168,85,247,0.25), transparent); align-self: stretch; min-height: 80px; }
@media (min-width: 900px) { .ui-hero-divider { display: block; } }

.ui-hero-right { display: flex; flex-direction: row; align-items: center; justify-content: center; flex-wrap: wrap; gap: 1.5rem; position: relative; z-index: 1; width: 100%; }
@media (min-width: 900px) { .ui-hero-right { flex-direction: column; align-items: flex-end; gap: 1rem; width: auto; flex-wrap: nowrap; } }

.ui-hero-usage { background: rgba(168,85,247,0.05); border: 1px solid rgba(168,85,247,0.15); border-radius: 0.875rem; padding: 0.875rem 1.25rem; min-width: 190px; }
.ui-hero-usage-bar-bg { background: rgba(0,0,0,0.5); border-radius: 9999px; height: 4px; overflow: hidden; margin: 0.4rem 0 0.3rem; }
.ui-hero-usage-bar-fill { height: 100%; border-radius: 9999px; background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899); box-shadow: 0 0 10px rgba(168,85,247,0.6); }

/* Live clock */
.ui-live-clock { font-size: 1.4rem; font-weight: 900; font-variant-numeric: tabular-nums; letter-spacing: -0.04em; line-height: 1; background: linear-gradient(135deg, #f1f5f9, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.ui-live-badge { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #34d399; background: rgba(52,211,153,0.08); border: 1px solid rgba(52,211,153,0.2); padding: 0.2rem 0.65rem; border-radius: 9999px; margin-bottom: 0.3rem; }
.ui-live-dot { width: 0.4rem; height: 0.4rem; border-radius: 50%; background: #34d399; box-shadow: 0 0 6px #34d399; animation: blink-live 1.2s ease-in-out infinite; }
@keyframes blink-live { 0%, 100% { opacity: 1; } 50% { opacity: 0.25; } }

/* BADGES */
.ui-status-dot { width: 0.45rem; height: 0.45rem; border-radius: 50%; display: inline-block; animation: pulse-dot 2s infinite; }
.ui-status-dot.green  { background: #34d399; box-shadow: 0 0 6px #34d399; }
.ui-status-dot.violet { background: #a855f7; box-shadow: 0 0 6px #a855f7; }
.ui-status-dot.red    { background: #f87171; box-shadow: 0 0 6px #f87171; }
@keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.75); } }

/* ALERT BANNER */
.ui-alert-banner { border-radius: 1.25rem; padding: 1rem 1.5rem; display: flex; align-items: flex-start; gap: 1.25rem; border: 1px solid; border-left-width: 4px; position: relative; overflow: hidden; animation: alert-slide 0.4s cubic-bezier(0.16,1,0.3,1) both; }
@keyframes alert-slide { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
.ui-alert-expired { background: linear-gradient(135deg, rgba(245,158,11,0.07), rgba(245,158,11,0.02)); border-color: rgba(245,158,11,0.2); border-left-color: #f59e0b; }
.ui-alert-bans    { background: linear-gradient(135deg, rgba(239,68,68,0.07), rgba(239,68,68,0.02));   border-color: rgba(239,68,68,0.2);  border-left-color: #f87171; }
.ui-alert-icon-box { width: 2.75rem; height: 2.75rem; border-radius: 0.875rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ui-alert-content { flex: 1; min-width: 0; }
.ui-alert-title { font-size: 0.875rem; font-weight: 800; color: white; margin-bottom: 0.2rem; }
.ui-alert-sub   { font-size: 0.78rem; color: rgba(148,163,184,0.7); line-height: 1.5; }

/* METRICS BAR */
.ui-metrics-bar { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
@media (min-width: 640px)  { .ui-metrics-bar { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1200px) { .ui-metrics-bar { grid-template-columns: 1.8fr 1fr 1fr 1fr 1fr 1fr; } }

.ui-metric-card { display: flex; flex-direction: column; align-items: center; text-align: center; background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.07); border-top: 2px solid var(--mc, #a855f7); border-radius: 1.25rem; padding: 1.25rem 1.5rem; position: relative; overflow: hidden; backdrop-filter: blur(12px); transition: border-color 0.3s, transform 0.25s, box-shadow 0.3s; }
.ui-metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 0% 0%, rgba(var(--mcr,168,85,247),0.06), transparent 60%); pointer-events: none; }
.ui-metric-card:hover { border-color: var(--mc, #a855f7); box-shadow: 0 12px 35px rgba(0,0,0,0.25), 0 0 0 1px var(--mc, #a855f7) inset; transform: translateY(-4px); }
.ui-metric-featured { }
@media (min-width: 640px)  { .ui-metric-featured { grid-column: span 3; } }
@media (min-width: 1200px) { .ui-metric-featured { grid-column: span 1; } }
.ui-metric-featured .ui-metric-num { font-size: 2.75rem; }
.ui-metric-featured-bar-bg  { width: 100%; height: 5px; border-radius: 9999px; background: rgba(0,0,0,0.5); overflow: hidden; margin-top: 0.875rem; margin-bottom: 0.35rem; }
.ui-metric-featured-bar-fill{ height: 100%; border-radius: 9999px; background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899); box-shadow: 0 0 12px rgba(168,85,247,0.6); }
.ui-metric-featured-sub { width: 100%; font-size: 0.65rem; color: rgba(148,163,184,0.4); display: flex; justify-content: space-between; }
.ui-metric-icon  { width: 2.5rem; height: 2.5rem; border-radius: 0.875rem; display: flex; align-items: center; justify-content: center; margin-bottom: 0.875rem; }
.ui-metric-num   { font-size: 2rem; font-weight: 900; color: var(--mc, white); line-height: 1; margin-bottom: 0.25rem; transition: all 0.3s; }
.ui-metric-label { font-size: 0.68rem; font-weight: 600; color: rgba(148,163,184,0.5); text-transform: uppercase; letter-spacing: 0.1em; }
.ui-metric-context { font-size: 0.62rem; color: rgba(148,163,184,0.4); margin-top: 0.2rem; }
@keyframes counter-pop { 0% { transform: scale(1); } 50% { transform: scale(1.15); filter: brightness(1.4); } 100% { transform: scale(1); } }
.counter-done { animation: counter-pop 0.35s ease; }

/* WARRANTY / LIST ROWS */
.ui-warranty-row { display: flex; align-items: center; gap: 1rem; padding: 0.875rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s; border-radius: 0.5rem; }
.ui-warranty-row:last-child { border-bottom: none; padding-bottom: 0; }
.ui-warranty-row:hover { background: rgba(168,85,247,0.03); }
.ui-warranty-icon { width: 2.25rem; height: 2.25rem; border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ui-warranty-info { flex: 1; min-width: 0; }
.ui-warranty-platform { font-size: 0.85rem; font-weight: 700; color: white; }
.ui-warranty-type { font-size: 0.72rem; color: rgba(148,163,184,0.55); margin-top: 0.1rem; }

/* QUICK LINKS */
.ui-quick-link { display: flex; align-items: center; gap: 1.25rem; padding: 1rem 1.25rem; border-radius: 1rem; border: 1px solid rgba(168,85,247,0.1); background: rgba(255,255,255,0.02); text-decoration: none; transition: all 0.3s cubic-bezier(0.16,1,0.3,1); color: rgba(148,163,184,0.9); margin-bottom: 0.75rem; text-align: left; width: 100%; }
.ui-quick-link:hover { background: linear-gradient(90deg, rgba(168,85,247,0.09), rgba(168,85,247,0.02)); border-color: rgba(168,85,247,0.35); color: white; transform: translateX(6px); box-shadow: 0 4px 20px rgba(168,85,247,0.08); }
.ui-quick-link:last-child { margin-bottom: 0; }
.ui-quick-link-icon { width: 2.75rem; height: 2.75rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: transform 0.3s; }
.ui-quick-link:hover .ui-quick-link-icon { transform: scale(1.12) rotate(6deg); }
.ui-quick-link-text { font-size: 0.95rem; font-weight: 700; margin-bottom: 0.1rem; color: white; }
.ui-quick-link-sub  { font-size: 0.74rem; color: rgba(148,163,184,0.5); }

/* DONUT */
.ui-donut-wrap { display: flex; flex-direction: column; align-items: center; gap: 1.5rem; text-align: center; }
@media (min-width: 640px) { .ui-donut-wrap { flex-direction: row; text-align: left; gap: 1.75rem; } }
.ui-donut-svg { flex-shrink: 0; filter: drop-shadow(0 0 10px rgba(168,85,247,0.35)); }
.ui-donut-center-text { text-anchor: middle; dominant-baseline: middle; }
.ui-donut-info { flex: 1; width: 100%; }
.ui-donut-stat-row { display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.85rem; }
.ui-donut-stat-row:last-child { border-bottom: none; }
.ui-donut-stat-label { color: rgba(148,163,184,0.65); font-weight: 500; }
.ui-donut-stat-val   { font-weight: 800; color: white; }

/* TOP CLIENTS RANKING */
.ui-rank-row { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0.875rem; border-radius: 0.875rem; transition: background 0.2s; margin-bottom: 0.5rem; border: 1px solid transparent; }
.ui-rank-row:hover { background: rgba(168,85,247,0.05); border-color: rgba(168,85,247,0.15); }
.ui-rank-row:last-child { margin-bottom: 0; }
.ui-rank-num { width: 1.75rem; height: 1.75rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 900; flex-shrink: 0; }
.ui-rank-bar-bg { flex: 1; height: 4px; border-radius: 9999px; background: rgba(255,255,255,0.06); overflow: hidden; margin-top: 0.3rem; }
.ui-rank-bar-fill { height: 100%; border-radius: 9999px; background: linear-gradient(90deg, #7c3aed, #a855f7); }

/* HEATMAP */
.ui-heatmap-bar { border-radius: 0.4rem; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; cursor: default; transition: transform 0.15s, filter 0.15s; }
.ui-heatmap-bar:hover { transform: scaleY(1.1) translateY(-2px); filter: brightness(1.3); }

/* SCANLINE on action card */
@keyframes scanline { 0% { transform: translateY(-100%); } 100% { transform: translateY(500%); } }
.ui-scanline { position: absolute; left: 0; right: 0; height: 25%; background: linear-gradient(180deg, transparent, rgba(168,85,247,0.03), transparent); pointer-events: none; z-index: 1; animation: scanline 5s linear infinite; }
</style>

<div wire:poll.30s class="space-y-6"
     x-data="{ notif: null, notifType: 'ok' }"
     @notif.window="notif = $event.detail.message; notifType = $event.detail.type ?? 'ok'; setTimeout(() => notif = null, 4500)">

    {{-- â•â• TOAST NOTIFICATION â•â• --}}
    <div x-show="notif"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-3 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 scale-95"
         class="fixed top-6 right-6 z-[999] rounded-2xl px-6 py-3.5 text-sm font-semibold shadow-2xl backdrop-blur-xl flex items-center gap-3"
         :class="notifType === 'error' ? 'bg-red-900/90 border border-red-500/50 text-red-300' : 'bg-emerald-900/90 border border-emerald-500/50 text-emerald-300'"
         style="display:none;">
        <span x-text="notifType === 'error' ? '❌' : '✅'"></span>
        <span x-text="notif"></span>
    </div>

    {{-- â• â• â• â• â• â• â• â• â• â• â• â• â• â•  HERO CARD â• â• â• â• â• â• â• â• â• â• â• â• â• â•  --}}
    <div class="ui-dash-hero ui-anim-in">
        <div class="ui-scanline"></div>

        <div class="ui-hero-left">
            <div class="ui-hero-avatar-wrap">
                <div class="ui-hero-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
            <div class="ui-hero-identity">
                <p class="ui-hero-greeting" x-data="{
                    getGreeting() {
                        const h = new Date().getHours();
                        return h < 12 ? '☀️ Buenos días' : (h < 19 ? '🌤️ Buenas tardes' : '🌙 Buenas noches');
                    }
                }" x-text="`${getGreeting()}, bienvenido de vuelta`"></p>
                <h1 class="ui-hero-name">{{ auth()->user()->name ?? 'Administrador' }}</h1>
                <p class="ui-hero-email">{{ auth()->user()->email }}</p>
                <div class="ui-hero-badges">
                    <span class="ui-badge ui-badge--success" style="padding:0.35rem 0.9rem;border-radius:9999px;font-size:0.7rem;"><span class="ui-status-dot green"></span> Sistema Online</span>
                    <span class="ui-badge ui-badge--slate" style="color:var(--ui-purple);border-color:rgba(168,85,247,0.25);padding:0.35rem 0.9rem;border-radius:9999px;font-size:0.68rem;"><span class="ui-status-dot violet"></span> Super Admin</span>
                    @if($this->metrics['totalBans'] > 0)
                        <span class="ui-badge ui-badge--error" style="padding:0.35rem 0.9rem;border-radius:9999px;font-size:0.68rem;"><span class="ui-status-dot red"></span> {{ $this->metrics['totalBans'] }} IPs bloqueadas</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="ui-hero-divider"></div>

        <div class="ui-hero-right">
            <div class="ui-hero-usage">
                <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(168,85,247,0.8);display:flex;justify-content:space-between;margin-bottom:0.1rem;">
                    Consultas Hoy <span style="color:#c4b5fd;">{{ $this->metrics['todayCount'] }}</span>
                </p>
                <div class="ui-hero-usage-bar-bg">
                    @php $pct = min(100, ($this->metrics['todayCount'] / max(1, $this->metrics['yesterdayCount'])) * 50); @endphp
                    <div class="ui-hero-usage-bar-fill" style="width:{{ $pct }}%;"></div>
                </div>
                <p style="font-size:0.65rem;color:rgba(148,163,184,0.45);">vs {{ $this->metrics['yesterdayCount'] }} ayer</p>
            </div>

            <div style="text-align:right;">
                <div class="ui-live-badge"><span class="ui-live-dot"></span> EN VIVO</div>
                <div class="ui-live-clock" id="admin-clock">{{ now()->format('H:i:s') }}</div>
                <div style="font-size:0.72rem;color:rgba(148,163,184,0.45);font-weight:500;margin-top:0.15rem;">
                    @php
                        $dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
                        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    @endphp
                    {{ $dias[now()->dayOfWeek] }}, {{ now()->day }} de {{ $meses[now()->month - 1] }}
                </div>
            </div>
        </div>
    </div>

    {{-- â• â•  ALERTAS CONTEXTUALES â• â•  --}}
    @if($this->metrics['expiredCount'] >= 3)
    <div class="ui-alert-banner ui-alert-expired ui-anim-in ui-delay-1">
        <div class="ui-alert-icon-box" style="background:rgba(245,158,11,0.12);">
            <svg width="22" height="22" fill="none" stroke="#f59e0b" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="ui-alert-content">
            <p class="ui-alert-title">⚠️ {{ $this->metrics['expiredCount'] }} clientes con suscripción vencida</p>
            <p class="ui-alert-sub">Hay clientes sin acceso activo. Usa "Renovar Vencidos" o gestiona individualmente desde la sección de clientes.</p>
        </div>
        <button wire:click="renewAllExpired" wire:loading.attr="disabled" wire:confirm="¿Renovar +30 días a todos los vencidos?"
                style="background:linear-gradient(135deg,#7c3aed,#ec4899);border:none;color:white;font-weight:700;font-size:0.75rem;padding:0.5rem 1.1rem;border-radius:0.75rem;cursor:pointer;flex-shrink:0;transition:all 0.2s;white-space:nowrap;">
            <span wire:loading wire:target="renewAllExpired">Renovando...</span>
            <span wire:loading.remove wire:target="renewAllExpired">Renovar Todos</span>
        </button>
    </div>
    @endif

    @if($this->metrics['totalBans'] >= 5)
    <div class="ui-alert-banner ui-alert-bans ui-anim-in ui-delay-1">
        <div class="ui-alert-icon-box" style="background:rgba(239,68,68,0.12);">
            <svg width="22" height="22" fill="none" stroke="#f87171" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div class="ui-alert-content">
            <p class="ui-alert-title">🛡️ {{ $this->metrics['totalBans'] }} IPs bloqueadas por Anti-Spam</p>
            <p class="ui-alert-sub">El sistema de protección ha detectado y bloqueado múltiples IPs. Revisa el panel de seguridad.</p>
        </div>
        <a href="{{ route('admin.ip-bans.index') }}" wire:navigate style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;font-weight:700;font-size:0.75rem;padding:0.5rem 1.1rem;border-radius:0.75rem;text-decoration:none;flex-shrink:0;white-space:nowrap;">
            Ver todas →
        </a>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-wrap items-center gap-2.5 ui-anim-in ui-delay-1">
        @foreach(['all' => '📊 Histórico', 'today' => '⚡ Hoy', 'week' => '📅 Esta Semana', 'month' => '🗓️ Este Mes'] as $val => $label)
            <button wire:click="$set('filter','{{ $val }}')"
                    class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider transition-all
                    {{ $filter === $val
                        ? 'bg-violet-600 text-white shadow-[0_0_20px_rgba(124,58,237,0.5)]'
                        : 'bg-white/[0.04] text-slate-400 hover:bg-white/[0.08] hover:text-white border border-white/10' }}">
                {{ $label }}
            </button>
        @endforeach
        <div wire:loading wire:target="filter" class="flex items-center gap-1.5 text-xs text-violet-400 ml-1">
            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Actualizando...
        </div>
    </div>

    {{-- â•â• KPI MÉTRICAS â•â• --}}
    <div class="ui-metrics-bar ui-anim-in ui-delay-2" wire:loading.class="opacity-60" wire:target="filter">

        <div class="ui-metric-card ui-metric-featured" style="--mc:#a855f7;--mcr:168,85,247;">
            <div class="ui-metric-icon" style="background:rgba(168,85,247,0.12);">
                <svg width="24" height="24" fill="none" stroke="#a855f7" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="ui-metric-num" id="cnt-queries" data-target="{{ $this->metrics['totalQueries'] }}">{{ number_format($this->metrics['totalQueries']) }}</div>
            <div class="ui-metric-label">Consultas Totales</div>
            <div class="ui-metric-featured-bar-bg"><div class="ui-metric-featured-bar-fill" style="width:100%"></div></div>
            <div class="ui-metric-featured-sub">
                <span>{{ $filter === 'all' ? 'Histórico completo' : 'Período seleccionado' }}</span>
                @if($filter !== 'all')
                    <span style="{{ $this->metrics['delta'] >= 0 ? 'color:#34d399' : 'color:#f87171' }}">
                        {{ $this->metrics['delta'] >= 0 ? '▲' : '▼' }} {{ abs($this->metrics['delta']) }}%
                    </span>
                @endif
            </div>
        </div>

        <div class="ui-metric-card" style="--mc:#ec4899;--mcr:236,72,153;">
            <div class="ui-metric-icon" style="background:rgba(236,72,153,0.12);">
                <svg width="20" height="20" fill="none" stroke="#ec4899" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="ui-metric-num" id="cnt-clients" data-target="{{ $this->metrics['totalClients'] }}">{{ number_format($this->metrics['totalClients']) }}</div>
            <div class="ui-metric-label">Clientes</div>
            <div class="ui-metric-context" style="color:#34d399;">{{ $this->metrics['activeClients'] }} activos · <span style="color:#f87171;">{{ $this->metrics['inactiveClients'] }} inactivos</span></div>
        </div>

        <div class="ui-metric-card" style="--mc:#38bdf8;--mcr:56,189,248;">
            <div class="ui-metric-icon" style="background:rgba(56,189,248,0.12);">
                <svg width="20" height="20" fill="none" stroke="#38bdf8" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div class="ui-metric-num" id="cnt-emails" data-target="{{ $this->metrics['totalEmails'] }}">{{ number_format($this->metrics['totalEmails']) }}</div>
            <div class="ui-metric-label">Correos Auth.</div>
            <div class="ui-metric-context">Lista blanca</div>
        </div>

        <div class="ui-metric-card" style="--mc:#10b981;--mcr:16,185,129;">
            <div class="ui-metric-icon" style="background:rgba(16,185,129,0.12);">
                <svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="ui-metric-num" id="cnt-users" data-target="{{ $this->metrics['totalUsers'] }}">{{ number_format($this->metrics['totalUsers']) }}</div>
            <div class="ui-metric-label">Usuarios</div>
            <div class="ui-metric-context">Del sistema</div>
        </div>

        <div class="ui-metric-card" style="--mc:#f59e0b;--mcr:245,158,11;">
            <div class="ui-metric-icon" style="background:rgba(245,158,11,0.12);">
                <svg width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div class="ui-metric-num" id="cnt-platforms" data-target="{{ $this->metrics['totalPlatforms'] }}">{{ number_format($this->metrics['totalPlatforms']) }}</div>
            <div class="ui-metric-label">Plataformas</div>
            <div class="ui-metric-context">Activas</div>
        </div>

        <div class="ui-metric-card" style="--mc:#f87171;--mcr:248,113,113;">
            <div class="ui-metric-icon" style="background:rgba(239,68,68,0.12);">
                <svg width="20" height="20" fill="none" stroke="#f87171" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div class="ui-metric-num" id="cnt-bans" data-target="{{ $this->metrics['totalBans'] }}" style="color:#f87171;">{{ number_format($this->metrics['totalBans']) }}</div>
            <div class="ui-metric-label">IPs Baneadas</div>
            <div class="ui-metric-context">Anti-spam</div>
        </div>
    </div>

    {{-- â•â• GRÍFICAS: BARRAS HOY + NUEVOS CLIENTES/MES â•â• --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 ui-anim-in ui-delay-2">
        <div>
            <div class="ae-card h-full">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap" style="color:#a855f7; background:rgba(168,85,247,0.15);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            Consultas por Hora — Hoy
                        </div>
                    </div>
                </div>
                <div class="ae-card-body">
                <div wire:ignore><div id="chart-queries-hour" style="min-height:220px;"></div></div></div></div></div>
        <div>
            <div class="ae-card h-full">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap" style="color:#ec4899; background:rgba(236,72,153,0.15);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            Nuevos Clientes — Últimos 6 Meses
                        </div>
                    </div>
                </div>
                <div class="ae-card-body">
                <div wire:ignore><div id="chart-monthly-clients" style="min-height:220px;"></div></div></div></div></div>
    </div>

    {{-- â•â• DONUT + HEATMAP â•â• --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 ui-anim-in ui-delay-3">

        {{-- Donut --}}
        <div>
            <div class="ae-card h-full">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap" style="color:#a855f7; background:rgba(168,85,247,0.15);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                            </div>
                            Distribución de Clientes
                        </div>
                    </div>
                </div>
                <div class="ae-card-body">
                <div class="ui-donut-wrap">
                    @php
                        $pct    = $this->clientStats['pct'];
                        $radius = 52; $circ = 2 * M_PI * $radius;
                        $offset = $circ - ($pct / 100) * $circ;
                    @endphp
                    <svg class="ui-donut-svg" width="130" height="130" viewBox="0 0 130 130">
                        <defs>
                            <linearGradient id="dg" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#7c3aed"/>
                                <stop offset="100%" style="stop-color:#ec4899"/>
                            </linearGradient>
                        </defs>
                        <circle cx="65" cy="65" r="{{ $radius }}" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="14"/>
                        <circle cx="65" cy="65" r="{{ $radius }}" fill="none" stroke="url(#dg)" stroke-width="14" stroke-linecap="round"
                                stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $offset }}" transform="rotate(-90 65 65)"
                                style="filter:drop-shadow(0 0 10px rgba(168,85,247,0.6));transition:stroke-dashoffset 1.2s ease;"/>
                        <text x="65" y="60" class="ui-donut-center-text" font-size="20" font-weight="900" fill="white" font-family="Inter,sans-serif">{{ $pct }}%</text>
                        <text x="65" y="78" class="ui-donut-center-text" font-size="9" font-weight="600" fill="rgba(148,163,184,0.5)" font-family="Inter,sans-serif">ACTIVOS</text>
                    </svg>
                    <div class="ui-donut-info">
                        <div class="ui-donut-stat-row"><span class="ui-donut-stat-label">Total clientes</span><span class="ui-donut-stat-val">{{ $this->clientStats['total'] }}</span></div>
                        <div class="ui-donut-stat-row"><span class="ui-donut-stat-label">Activos</span><span class="ui-donut-stat-val" style="color:#34d399;">{{ $this->clientStats['active'] }}</span></div>
                        <div class="ui-donut-stat-row"><span class="ui-donut-stat-label">Inactivos</span><span class="ui-donut-stat-val" style="color:#f87171;">{{ $this->clientStats['inactive'] }}</span></div>
                        <div class="ui-donut-stat-row"><span class="ui-donut-stat-label">Tasa de actividad</span><span class="ui-donut-stat-val" style="color:#a855f7;">{{ $pct }}%</span></div></div></div></div></div></div>

        {{-- Heatmap --}}
        <div>
            <div class="ae-card h-full">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap" style="color:#a855f7; background:rgba(168,85,247,0.15);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Horarios de Mayor Actividad (7 días)
                        </div>
                    </div>
                </div>
                <div class="ae-card-body">
                <div style="display:flex;align-items:flex-end;gap:4px;flex-wrap:nowrap;overflow:hidden;">
                    @foreach($this->heatmap as $cell)
                        @php
                            $op  = 0.07 + ($cell['intensity'] * 0.93);
                            $col = "rgba(168,85,247,{$op})";
                            $brd = $cell['intensity'] > 0.6 ? 'rgba(168,85,247,0.7)' : 'rgba(168,85,247,0.15)';
                            $h   = max(24, 24 + intval($cell['intensity'] * 60));
                        @endphp
                        <div class="ui-heatmap-bar" title="{{ str_pad($cell['hour'],2,'0',STR_PAD_LEFT) }}:00 — {{ $cell['count'] }} consultas"
                             style="background:{{ $col }};border:1px solid {{ $brd }};flex:1;min-width:0;height:{{ $h }}px;">
                            @if($cell['count'] > 0)
                                <span style="font-size:0.5rem;font-weight:700;color:rgba(255,255,255,0.6);margin-bottom:2px;writing-mode:horizontal-tb;">{{ $cell['count'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:0.5rem;">
                    <span style="font-size:0.6rem;color:rgba(100,116,139,0.5);">0h</span>
                    <span style="font-size:0.6rem;color:rgba(100,116,139,0.5);">6h</span>
                    <span style="font-size:0.6rem;color:rgba(100,116,139,0.5);">12h</span>
                    <span style="font-size:0.6rem;color:rgba(100,116,139,0.5);">18h</span>
                    <span style="font-size:0.6rem;color:rgba(100,116,139,0.5);">23h</span></div></div></div></div></div>

    {{-- â•â• CONSULTAS RECIENTES + PANEL DERECHO â•â• --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 ui-anim-in ui-delay-3">

        {{-- Consultas Recientes --}}
        <div class="xl:col-span-2 ae-card h-full">
            <div class="ae-card-head">
                <div class="flex items-center justify-between w-full">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap" style="color:#a855f7; background:rgba(168,85,247,0.15);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Consultas Recientes
                        </div>
                    </div>
                    <a href="{{ route('admin.queries.index') }}" wire:navigate style="font-size:0.75rem;color:#a855f7;font-weight:700;display:flex;align-items:center;gap:4px;text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='#a855f7'">
                        Ver todo <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <div class="ae-card-body">

            {{-- â–º BÚSQUEDA EN TIEMPO REAL --}}
            <div class="ui-search-wrap" style="margin-bottom:0.75rem;">
                <div class="ui-search-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="ui-search"
                    placeholder="Buscar por email, cliente o plataforma..."
                />
                <div wire:loading wire:target="search" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);">
                    <svg class="animate-spin" width="14" height="14" fill="none" stroke="#a855f7" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
                @if($search)
                <button wire:click="$set('search','')"
                        style="position:absolute;right:0.875rem;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(148,163,184,0.5);cursor:pointer;font-size:1rem;line-height:1;padding:0;">
                    &times;
                </button>
                @endif
            </div>
            <div style="padding-top:1rem;"><div style="display:flex;flex-direction:column;gap:0.5rem;" wire:loading.class="opacity-50" wire:target="search,filter">
                    @forelse($this->recentQueries as $query)
                    <div wire:key="query-{{ $query->id }}" style="display:flex;align-items:center;gap:0.875rem;padding:0.75rem;border-radius:0.875rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(168,85,247,0.25)';this.style.background='rgba(168,85,247,0.03)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.05)';this.style.background='rgba(255,255,255,0.02)'">
                        <div style="width:2.25rem;height:2.25rem;border-radius:0.625rem;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:0.85rem;background:linear-gradient(135deg,rgba(124,58,237,0.3),rgba(236,72,153,0.2));border:1px solid rgba(168,85,247,0.3);color:#c4b5fd;">
                            {{ strtoupper(substr($query->platform->name ?? '?', 0, 1)) }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:0.875rem;font-weight:600;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $query->email }}</p>
                            <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.15rem;">
                                <span style="font-size:0.62rem;background:rgba(168,85,247,0.1);border:1px solid rgba(168,85,247,0.2);color:#c4b5fd;padding:0.1rem 0.5rem;border-radius:9999px;font-weight:600;">{{ $query->platform->name ?? 'N/A' }}</span>
                                @if($query->client)<span style="font-size:0.62rem;color:rgba(100,116,139,0.7);">{{ $query->client->name }}</span>@endif
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.25rem;flex-shrink:0;">
                            <span style="font-size:0.62rem;color:rgba(100,116,139,0.6);">{{ $query->created_at->diffForHumans() }}</span>
                            @if($query->result === 'success')
                                <span style="font-size:0.62rem;font-weight:700;color:var(--ui-success-2);background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.25);padding:0.1rem 0.5rem;border-radius:9999px;">✓ Éxito</span>
                            @elseif($query->result === 'no_code')
                                <span style="font-size:0.62rem;font-weight:700;color:#fbbf24;background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.25);padding:0.1rem 0.5rem;border-radius:9999px;">Sin Código</span>
                            @else
                                <span style="font-size:0.62rem;font-weight:700;color:var(--ui-error-2);background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);padding:0.1rem 0.5rem;border-radius:9999px;">Error</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="ui-empty" style="padding:2.5rem;">
                        <svg class="ui-empty-icon" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.25;margin-bottom:0.75rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        <p style="font-size:0.875rem;color:rgba(100,116,139,0.5);">No hay consultas en este período</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        </div>

        {{-- Panel Derecho --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- ACCIONES RÍPIDAS --}}
            <div>
                <div class="ae-card h-full">
                    <div class="ae-card-head">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#a855f7; background:rgba(168,85,247,0.15);">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                Acciones Rápidas
                            </div>
                        </div>
                    </div>
                    <div class="ae-card-body">
                    <a href="{{ route('admin.clients.create') }}" wire:navigate class="ui-quick-link">
                        <div class="ui-quick-link-icon" style="background:rgba(236,72,153,0.12);">
                            <svg width="18" height="18" fill="none" stroke="#ec4899" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div><div class="ui-quick-link-text">Nuevo Cliente</div><div class="ui-quick-link-sub">Registrar acceso</div></div>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="opacity:0.25;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('admin.allowed-emails.create') }}" wire:navigate class="ui-quick-link">
                        <div class="ui-quick-link-icon" style="background:rgba(168,85,247,0.12);">
                            <svg width="18" height="18" fill="none" stroke="#a855f7" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div><div class="ui-quick-link-text">Autorizar Email</div><div class="ui-quick-link-sub">Dar acceso masivo</div></div>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="opacity:0.25;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('admin.platforms.create') }}" wire:navigate class="ui-quick-link">
                        <div class="ui-quick-link-icon" style="background:rgba(56,189,248,0.12);">
                            <svg width="18" height="18" fill="none" stroke="#38bdf8" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <div><div class="ui-quick-link-text">Nueva Plataforma</div><div class="ui-quick-link-sub">Agregar fuente</div></div>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="opacity:0.25;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <button wire:click="renewAllExpired" wire:loading.attr="disabled" wire:confirm="¿Renovar +30 días a todos los vencidos?" class="ui-quick-link" style="margin-bottom:0;">
                        <div class="ui-quick-link-icon" style="background:rgba(245,158,11,0.12);">
                            <span wire:loading wire:target="renewAllExpired"><svg class="animate-spin" width="18" height="18" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></span>
                            <span wire:loading.remove wire:target="renewAllExpired"><svg width="18" height="18" fill="none" stroke="#f59e0b" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></span>
                        </div>
                        <div><div class="ui-quick-link-text">Renovar Vencidos</div><div class="ui-quick-link-sub">+30 días a todos</div></div>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="opacity:0.25;margin-left:auto;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                </div>
            </div>

            {{-- VENCEN PRONTO --}}
            <div>
                <div class="ae-card h-full">
                    <div class="ae-card-head">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#f59e0b; background:rgba(245,158,11,0.15);">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                Vencen Pronto
                            </div>
                        </div>
                    </div>
                    <div class="ae-card-body">
                    @forelse($this->expiringClients as $client)
                    <div class="ui-warranty-row">
                        <div class="ui-warranty-icon" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);">
                            <svg width="16" height="16" fill="none" stroke="#fbbf24" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="ui-warranty-info">
                            <div class="ui-warranty-platform">{{ $client->name }}</div>
                            <div class="ui-warranty-type">{{ \Carbon\Carbon::parse($client->allowedEmails->first()->pivot->expires_at)->diffForHumans() }}</div>
                        </div>
                        <a href="{{ route('admin.clients.edit', $client->id) }}" wire:navigate class="ui-badge ui-badge--slate" style="color:#fbbf24;border-color:rgba(251,191,36,0.25);">Cobrar</a>
                    </div>
                    @empty
                    <div style="text-align:center;padding:1.5rem 0;color:rgba(52,211,153,0.65);">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5;margin:0 auto 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p style="font-size:0.8rem;">Sin renovaciones próximas ✓</p>
                    </div>
                    @endforelse
                </div>
                </div>
            </div>

        </div>
    </div>

    {{-- â•â• RANKING TOP CLIENTES + ANTI-SPAM â•â• --}}
    <div class="grid grid-cols-1 {{ auth()->id() === 1 ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }} gap-6 ui-anim-in ui-delay-4">

        {{-- Top 5 Clientes --}}
        <div>
            <div class="ae-card h-full">
                <div class="ae-card-head">
                    <div class="ae-card-title">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="ui-icon-wrap" style="color:#f59e0b; background:rgba(245,158,11,0.15);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            Top 5 — Clientes Más Activos
                        </div>
                    </div>
                </div>
                <div class="ae-card-body">
                @php $maxQ = $this->topClients->max('query_count') ?: 1; @endphp
                @forelse($this->topClients as $i => $client)
                @php
                    $colors = ['#f59e0b','#94a3b8','#f97316','#a855f7','#38bdf8'];
                    $medals = ['🥇','🥈','🥉','4°','5°'];
                    $c = $colors[$i] ?? '#a855f7';
                @endphp
                <div class="ui-rank-row">
                    <div class="ui-rank-num" style="background:rgba({{ $i===0?'245,158,11':($i===1?'148,163,184':'168,85,247') }},0.15);color:{{ $c }};">
                        {{ $medals[$i] }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem;">
                            <span style="font-size:0.875rem;font-weight:700;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:70%;">{{ $client->name }}</span>
                            <span style="font-size:0.75rem;font-weight:800;color:{{ $c }};">{{ number_format($client->query_count) }}</span>
                        </div>
                        <div class="ui-rank-bar-bg">
                            <div class="ui-rank-bar-fill" style="width:{{ $maxQ > 0 ? ($client->query_count/$maxQ)*100 : 0 }}%;background:linear-gradient(90deg,{{ $c }},{{ $c }}88);"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:1.5rem 0;color:rgba(100,116,139,0.5);">
                    <p style="font-size:0.875rem;">Sin datos disponibles</p>
                </div>
                @endforelse
                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.05);">
                    <a href="{{ route('admin.clients.index') }}" wire:navigate style="font-size:0.75rem;color:#a855f7;font-weight:700;text-decoration:none;">Ver todos los clientes →</a>
                </div>
            </div>
        </div>
        </div>

        {{-- Anti-Spam --}}
        @if(auth()->id() === 1)
        <div>
            <div class="ae-card h-full" style="border-left:2px solid rgba(239,68,68,0.5);">
                <div class="ae-card-head">
                    <div class="flex items-center justify-between w-full">
                        <div class="ae-card-title">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ui-icon-wrap" style="color:#f87171; background:rgba(239,68,68,0.15);">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                Anti-Spam — IPs Bloqueadas
                            </div>
                        </div>
                        @if($this->securityThreats->count() > 0)
                            <a href="{{ route('admin.ip-bans.index') }}" wire:navigate style="font-size:0.75rem;color:#f87171;font-weight:700;text-decoration:none;">Ver todas →</a>
                        @endif
                    </div>
                </div>
                <div class="ae-card-body">
                @forelse($this->securityThreats as $ban)
                <div class="ui-warranty-row">
                    <div class="ui-warranty-icon" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);">
                        <svg width="14" height="14" fill="none" stroke="#f87171" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div class="ui-warranty-info">
                        <div class="ui-warranty-platform" style="font-family:ui-monospace,monospace;font-size:0.8rem;letter-spacing:0.05em;">{{ $ban->ip_address }}</div>
                        <div class="ui-warranty-type">{{ $ban->created_at->diffForHumans() }}
                            @if($ban->reason ?? false) Â· <span style="color:rgba(248,113,113,0.7);">{{ $ban->reason }}</span>@endif
                        </div>
                    </div>
                    <button wire:click="unbanIp({{ $ban->id }})" wire:loading.attr="disabled" wire:confirm="¿Desbanear la IP {{ $ban->ip_address }}?" class="ui-btn-ghost" style="padding:0.25rem 0.7rem;font-size:0.65rem;color:var(--ui-success-2);border-color:rgba(52,211,153,0.3);">
                        <span wire:loading wire:target="unbanIp({{ $ban->id }})">...</span>
                        <span wire:loading.remove wire:target="unbanIp({{ $ban->id }})">✓</span>
                        Desbanear
                    </button>
                </div>
                @empty
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem 0;color:rgba(52,211,153,0.65);">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5;margin-bottom:0.75rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p style="font-size:0.875rem;font-weight:600;">🛡️ Sistema 100% seguro</p>
                    <p style="font-size:0.75rem;margin-top:0.25rem;opacity:0.6;">No hay IPs bloqueadas</p></div>@endforelse</div></div></div>@endif

    </div>
</div>

{{-- â•â• SCRIPTS â•â• --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Reloj en vivo
(function() {
    function tick() {
        var el = document.getElementById('admin-clock');
        if (el) el.textContent = new Date().toLocaleTimeString('es',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
    }
    tick(); setInterval(tick, 1000);
})();

// Counter pop animation
(function() {
    var els = document.querySelectorAll('[data-target]');
    els.forEach(function(el) {
        var target = parseInt(el.dataset.target) || 0;
        if (target === 0) return;
        var start = 0, duration = 900, step = target / (duration / 16);
        var iv = setInterval(function() {
            start = Math.min(start + step, target);
            el.textContent = Math.floor(start).toLocaleString();
            if (start >= target) { clearInterval(iv); el.classList.add('counter-done'); }
        }, 16);
    });
})();

// ApexCharts
var chartData    = @json($this->chartData);
var monthlyData  = @json($this->monthlyClients);
var clientData   = @json($this->clientStats);

function initCharts() {
    // â”€â”€ Barras: consultas por hora â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    var barEl = document.getElementById('chart-queries-hour');
    if (barEl && !barEl._apex) {
        barEl._apex = new ApexCharts(barEl, {
            series: [{ name: 'Consultas', data: chartData.values }],
            chart: { type: 'bar', height: 220, toolbar: { show: false }, background: 'transparent', foreColor: '#64748b',
                     animations: { enabled: true, easing: 'easeinout', speed: 700 } },
            colors: ['#a855f7'],
            fill: { type: 'gradient', gradient: { type: 'vertical', colorStops: [
                { offset: 0,   color: '#a855f7', opacity: 1   },
                { offset: 100, color: '#ec4899', opacity: 0.5 }
            ]}},
            plotOptions: { bar: { borderRadius: 6, columnWidth: '58%' } },
            xaxis: { categories: chartData.labels, labels: { style: { fontSize: '10px', colors: '#64748b' }, rotate: 0, hideOverlappingLabels: true } },
            yaxis: { labels: { style: { fontSize: '10px', colors: '#64748b' } } },
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark', y: { formatter: val => val + ' consultas' } }
        });
        barEl._apex.render();
    }

    // â”€â”€ Írea: nuevos clientes por mes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    var monthEl = document.getElementById('chart-monthly-clients');
    if (monthEl && !monthEl._apex) {
        monthEl._apex = new ApexCharts(monthEl, {
            series: [{ name: 'Nuevos clientes', data: monthlyData.map(m => m.count) }],
            chart: { type: 'area', height: 220, toolbar: { show: false }, background: 'transparent', foreColor: '#64748b',
                     animations: { enabled: true, easing: 'easeinout', speed: 700 } },
            colors: ['#ec4899'],
            fill: { type: 'gradient', gradient: { type: 'vertical', shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.02, stops: [0, 100] } },
            stroke: { width: 2.5, curve: 'smooth' },
            xaxis: { categories: monthlyData.map(m => m.label), labels: { style: { fontSize: '11px', colors: '#64748b' } } },
            yaxis: { labels: { style: { fontSize: '10px', colors: '#64748b' } }, min: 0, tickAmount: 4, forceNiceScale: true },
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            markers: { size: 4, colors: ['#ec4899'], strokeColors: '#0f0823', strokeWidth: 2 },
            tooltip: { theme: 'dark', y: { formatter: val => val + ' clientes' } }
        });
        monthEl._apex.render();
    }
}

document.addEventListener('DOMContentLoaded', initCharts);
document.addEventListener('livewire:navigated', initCharts);

window.addEventListener('filter-updated', function(e) {
    var cData = e.detail.chartData || (e.detail[0] && e.detail[0].chartData);
    if (cData) {
        var barEl = document.getElementById('chart-queries-hour');
        if (barEl && barEl._apex) {
            barEl._apex.updateSeries([{ name: 'Consultas', data: cData.values }]);
            barEl._apex.updateOptions({ xaxis: { categories: cData.labels } });
        }
    }
});
</script>
