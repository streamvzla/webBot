<div class="space-y-5"
     x-data="{ notif: null }"
     @notif.window="notif = $event.detail.message; setTimeout(() => notif = null, 4000)">

{{-- ════════════════════════════════════════════════════
     ALLOWED EMAILS — ENTERPRISE SaaS EDITION
════════════════════════════════════════════════════ --}}
<style>
/* ── CHECKBOX ── */
.ae-check { appearance:none;width:1.1rem;height:1.1rem;border-radius:0.25rem;border:1px solid rgba(148,163,184,0.25);background:transparent;cursor:pointer;position:relative;transition:all 0.15s;flex-shrink:0; }
.ae-check:checked { background:#7c3aed;border-color:#7c3aed; }
.ae-check:checked::after { content:'';position:absolute;left:5px;top:2px;width:4px;height:7px;border:solid white;border-width:0 1.5px 1.5px 0;transform:rotate(45deg); }
.ae-check:hover:not(:checked) { border-color:rgba(148,163,184,0.5); }

/* ── MÉTRICAS GLASS ── */
.ae-metric { flex:1;min-width:130px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:0.875rem;padding:1rem 1.35rem;display:flex;align-items:center;gap:0.85rem;backdrop-filter:blur(16px);transition:border-color 0.2s; }
.ae-metric:hover { border-color:rgba(255,255,255,0.12); }
.ae-metric-num { font-size:1.6rem;font-weight:900;letter-spacing:-0.05em;line-height:1; }
.ae-metric-label { font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(148,163,184,0.4);margin-top:0.3rem; }
.ae-metric-icon { flex-shrink:0;width:2rem;height:2rem;border-radius:0.5rem;display:flex;align-items:center;justify-content:center; }

/* ── TOOLBAR ── */
.ae-toolbar { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:0.85rem 1rem;display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;backdrop-filter:blur(16px); }
.ae-search-wrap { position:relative;flex:1;min-width:200px; }
.ae-search-icon { position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:rgba(148,163,184,0.35);pointer-events:none; }
.ae-search { width:100%;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:0.625rem;padding:0.55rem 0.875rem 0.55rem 2.5rem;color:white;font-size:0.85rem;outline:none;transition:all 0.2s; }
.ae-search::placeholder { color:rgba(148,163,184,0.35); }
.ae-search:focus { border-color:rgba(124,58,237,0.4);background:rgba(124,58,237,0.04); }
.ae-select { background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:0.625rem;padding:0.55rem 0.875rem;color:rgba(241,245,249,0.85);font-size:0.82rem;outline:none;cursor:pointer;transition:all 0.2s;min-width:130px; }
.ae-select:focus { border-color:rgba(124,58,237,0.4);background:rgba(124,58,237,0.04); }
.ae-select option { background:#0f0a28;color:white; }
.ae-divider { width:1px;height:1.5rem;background:rgba(255,255,255,0.08);flex-shrink:0; }
.ae-view-grp { display:flex;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:0.5rem;padding:0.2rem;gap:0.1rem; }
.ae-view-btn { display:flex;align-items:center;justify-content:center;width:1.9rem;height:1.9rem;border-radius:0.35rem;background:transparent;color:rgba(148,163,184,0.4);border:none;cursor:pointer;transition:all 0.2s; }
.ae-view-btn:hover { color:rgba(241,245,249,0.9); }
.ae-view-btn.active { background:rgba(124,58,237,0.15);color:#a78bfa; }

/* ── EMAIL CARDS ── */
.ae-card { background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:0.875rem;overflow:hidden;transition:all 0.2s;display:flex;flex-direction:column; }
.ae-card:hover { border-color:rgba(255,255,255,0.12);transform:translateY(-1px);box-shadow:0 8px 32px rgba(0,0,0,0.3); }
.ae-card.selected { border-color:rgba(124,58,237,0.4);background:rgba(124,58,237,0.04); }
.ae-card-head { padding:1rem 1.1rem 0.75rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,0.05); }
.ae-card-body { padding:0.875rem 1.1rem;flex:1; }
.ae-card-foot { padding:0.6rem 1.1rem;border-top:1px solid rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:space-between; }
.ae-monogram { width:2.25rem;height:2.25rem;border-radius:0.5rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;font-size:0.95rem;font-weight:800;color:rgba(241,245,249,0.7);flex-shrink:0; }
.ae-email { font-size:0.88rem;font-weight:700;color:white;word-break:break-all;line-height:1.3;margin-bottom:0.5rem; }
.ae-meta-grid { display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-top:0.75rem; }
.ae-meta-cell { background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.05);border-radius:0.5rem;padding:0.5rem 0.625rem; }
.ae-meta-key { font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(148,163,184,0.4);margin-bottom:0.2rem; }
.ae-meta-val { font-size:0.85rem;font-weight:800;line-height:1; }
.ae-edit-btn { display:inline-flex;align-items:center;gap:0.35rem;font-size:0.72rem;font-weight:600;color:rgba(148,163,184,0.5);padding:0.35rem 0.6rem;border-radius:0.4rem;text-decoration:none;border:1px solid transparent;transition:all 0.2s; }
.ae-edit-btn:hover { color:white;background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.1); }

/* ── BULK BAR ── */
.ae-bulk { position:fixed;bottom:1.75rem;left:50%;transform:translateX(-50%) translateY(80px);opacity:0;background:rgba(8,6,20,0.98);border:1px solid rgba(124,58,237,0.25);backdrop-filter:blur(20px);border-radius:0.875rem;padding:0.6rem 1.1rem;display:flex;align-items:center;gap:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.7);z-index:999;transition:all 0.3s cubic-bezier(0.4,0,0.2,1); }
.ae-bulk.visible { transform:translateX(-50%) translateY(0);opacity:1; }
.ae-bulk-count { font-size:0.8rem;font-weight:700;color:rgba(167,139,250,0.9);background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.25);border-radius:0.35rem;padding:0.2rem 0.5rem;white-space:nowrap; }
.ae-bulk-sep { width:1px;height:1.25rem;background:rgba(255,255,255,0.08); }
.ae-bulk-btn { display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:0.45rem;background:transparent;color:rgba(255,255,255,0.5);transition:all 0.2s;cursor:pointer;border:1px solid rgba(255,255,255,0.06); }
.ae-bulk-btn:hover { transform:translateY(-1px);color:white;border-color:rgba(255,255,255,0.15); }
.ae-bulk-btn.g:hover { background:rgba(16,185,129,0.12);color:#34d399; }
.ae-bulk-btn.a:hover { background:rgba(245,158,11,0.12);color:#fbbf24; }
.ae-bulk-btn.b:hover { background:rgba(59,130,246,0.12);color:#60a5fa; }
.ae-bulk-btn.s:hover { background:rgba(100,116,139,0.12);color:#94a3b8; }
.ae-bulk-btn.r:hover { background:rgba(244,63,94,0.12);color:#fb7185; }
.ae-assign-wrap { display:flex;align-items:center;gap:0.4rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:0.45rem;padding:0.2rem 0.3rem 0.2rem 0.6rem; }
.ae-assign-select { background:transparent;border:none;color:rgba(255,255,255,0.8);font-size:0.72rem;font-weight:600;outline:none;cursor:pointer;max-width:140px; }
.ae-assign-apply { font-size:0.7rem;font-weight:700;color:white;background:rgba(124,58,237,0.3);border:1px solid rgba(124,58,237,0.3);border-radius:0.3rem;padding:0.2rem 0.6rem;cursor:pointer;transition:all 0.2s; }
.ae-assign-apply:hover { background:rgba(124,58,237,0.5); }

/* ── TABLE ── */
.ae-table { width:100%;border-collapse:collapse; }
.ae-table thead tr th { padding:0.75rem 1rem;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:rgba(148,163,184,0.45);text-align:left;border-bottom:1px solid rgba(255,255,255,0.06); }
.ae-table tbody tr { border-bottom:1px solid rgba(255,255,255,0.04);transition:background 0.15s; }
.ae-table tbody tr:hover { background:rgba(255,255,255,0.025); }
.ae-table tbody tr:last-child { border-bottom:none; }
.ae-table tbody td { padding:0.875rem 1rem;vertical-align:middle; }
.ae-tbl-monogram { width:2rem;height:2rem;border-radius:0.4rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:800;color:rgba(241,245,249,0.65);flex-shrink:0; }

/* ── TOAST ── */
.ae-toast { position:fixed;top:1.5rem;right:1.5rem;background:rgba(10,8,24,0.97);border:1px solid rgba(52,211,153,0.3);border-radius:0.75rem;padding:0.75rem 1.1rem;display:flex;align-items:center;gap:0.75rem;font-size:0.85rem;font-weight:600;color:white;box-shadow:0 16px 40px rgba(0,0,0,0.5);z-index:1000;transition:all 0.3s;transform:translateX(110%);opacity:0; }
.ae-toast.show { transform:translateX(0);opacity:1; }
</style>

    {{-- TOAST --}}
    <div x-show="notif" class="ae-toast" :class="{'show': notif}" style="display:none;">
        <svg width="18" height="18" fill="none" stroke="#34d399" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span x-text="notif"></span>
    </div>

    {{-- ══ BULK ACTIONS BAR ══ --}}
    <div class="ae-bulk {{ count($selectedIds) > 0 ? 'visible' : '' }}">
        <span class="ae-bulk-count">{{ count($selectedIds) }} seleccionados</span>
        <div class="ae-bulk-sep"></div>
        <div style="display:flex;align-items:center;gap:0.35rem;">
            <button wire:click="activateSelected"   onclick="confirm('¿Activar seleccionados?')||event.stopImmediatePropagation()"           class="ae-bulk-btn g" title="Activar"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button>
            <button wire:click="deactivateSelected" onclick="confirm('¿Desactivar seleccionados?')||event.stopImmediatePropagation()"         class="ae-bulk-btn a" title="Desactivar"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button>
            <button wire:click="makePublicSelected" onclick="confirm('¿Hacer públicos?')||event.stopImmediatePropagation()"                   class="ae-bulk-btn b" title="Público"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg></button>
            <button wire:click="makePrivateSelected" onclick="confirm('¿Hacer privados?')||event.stopImmediatePropagation()"                  class="ae-bulk-btn s" title="Privado"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></button>
        </div>
        <div class="ae-bulk-sep"></div>
        <div class="ae-assign-wrap">
            <select wire:model="assignToUserId" class="ae-assign-select">
                <option value="" style="background:#0f0a28;">Asignar a...</option>
                <option value="unassign" style="background:#0f0a28;">Quitar asignación</option>
                @foreach($teamMembers as $member)
                    <option value="{{ $member->id }}" style="background:#0f0a28;">{{ $member->username }}</option>
                @endforeach
            </select>
            <button wire:click="assignSelectedToUser" class="ae-assign-apply">Aplicar</button>
        </div>
        <div class="ae-bulk-sep"></div>
        <button wire:click="deleteSelected" onclick="confirm('¿Eliminar definitivamente?')||event.stopImmediatePropagation()" class="ae-bulk-btn r" title="Eliminar"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
    </div>

    {{-- ══ MÉTRICAS GLASS + ACCIÓN ══ --}}
    <div class="ui-anim-in" style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;">

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(148,163,184,0.07);">
                <svg width="16" height="16" fill="none" stroke="rgba(148,163,184,0.55)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:white;">{{ $this->stats['total'] ?? 0 }}</div>
                <div class="ae-metric-label">Total</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(52,211,153,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(52,211,153,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#34d399;">{{ $this->stats['active'] ?? 0 }}</div>
                <div class="ae-metric-label">Activos</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(248,113,113,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(248,113,113,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#f87171;">{{ $this->stats['inactive'] ?? 0 }}</div>
                <div class="ae-metric-label">Inactivos</div>
            </div>
        </div>

        <div class="ae-metric">
            <div class="ae-metric-icon" style="background:rgba(56,189,248,0.08);">
                <svg width="16" height="16" fill="none" stroke="rgba(56,189,248,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#38bdf8;">{{ $this->stats['public'] ?? 0 }}</div>
                <div class="ae-metric-label">Públicos</div>
            </div>
        </div>

        <div style="display:flex;align-items:center;flex-shrink:0;">
            <a wire:navigate href="{{ route('admin.allowed-emails.create') }}" class="ui-btn ui-btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nuevo Correo
            </a>
        </div>

    </div>

    {{-- ══ TOOLBAR DE FILTROS ══ --}}
    <div class="ae-toolbar ui-anim-in ui-delay-1">

        <div class="ae-search-wrap">
            <div class="ae-search-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
            <input wire:model.live.debounce.300ms="search" type="text" class="ae-search" placeholder="Buscar correo...">
        </div>

        <select wire:model.live="assignment" class="ae-select">
            <option value="">Asignaciones</option>
            <option value="free">Libres</option>
            <option value="assigned">Ocupadas</option>
            <option value="expired">Con vencidas</option>
        </select>

        <select wire:model.live="public" class="ae-select" style="min-width:120px;">
            <option value="">Privacidad</option>
            <option value="1">Públicos</option>
            <option value="0">Privados</option>
        </select>

        <select wire:model.live="platform_id" class="ae-select">
            <option value="">Plataformas</option>
            @foreach($platforms as $platform)
                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
            @endforeach
        </select>

        @if(auth()->user()->role === 'admin')
        <select wire:model.live="user_id" class="ae-select">
            <option value="">Usuarios</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
        @endif

        <div class="ae-divider"></div>

        <div class="ae-view-grp">
            <button wire:click="$set('view','cards')" class="ae-view-btn {{ $view==='cards' ? 'active' : '' }}" title="Tarjetas">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/></svg>
            </button>
            <button wire:click="$set('view','table')" class="ae-view-btn {{ $view==='table' ? 'active' : '' }}" title="Tabla">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/></svg>
            </button>
        </div>

    </div>

    {{-- ══ RESULTADOS ══ --}}
    <div class="ui-anim-in ui-delay-2" wire:loading.class="opacity-50" wire:target="search,assignment,public,platform_id,user_id,view">

        @if($allowedEmails->isEmpty())
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:4rem 2rem;text-align:center;">
            <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">Sin resultados</p>
            <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">Ajusta los filtros o registra un nuevo correo.</p>
        </div>

        @else

        {{-- VISTA CARDS --}}
        @if($view === 'cards')
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:0.875rem;">
            @foreach($allowedEmails as $email)
            <div wire:key="ec-{{ $email->id }}" class="ae-card {{ in_array($email->id, $selectedIds) ? 'selected' : '' }}">

                {{-- Head: checkbox + monogram + badges --}}
                <div class="ae-card-head">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <input type="checkbox" wire:model.live="selectedIds" value="{{ $email->id }}" class="ae-check">
                        <div class="ae-monogram">{{ strtoupper(substr($email->email, 0, 1)) }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.4rem;">
                        @if($email->is_active)
                            <span class="ui-badge ui-badge--success">Activo</span>
                        @else
                            <span class="ui-badge ui-badge--error">Inactivo</span>
                        @endif
                        @if($email->is_public)
                            <span class="ui-badge ui-badge--info">Público</span>
                        @else
                            <span class="ui-badge ui-badge--neutral">Privado</span>
                        @endif
                    </div>
                </div>

                {{-- Body: email + platform + stats --}}
                <div class="ae-card-body">
                    <div class="ae-email">{{ $email->email }}</div>
                    <div style="margin-bottom:0.625rem;">
                        @if($email->platform)
                            <span class="ui-badge ui-badge--violet">{{ $email->platform->name }}</span>
                        @else
                            <span style="font-size:0.72rem;color:rgba(148,163,184,0.35);">Sin plataforma</span>
                        @endif
                    </div>
                    <div class="ae-meta-grid">
                        <div class="ae-meta-cell">
                            <div class="ae-meta-key">Stock</div>
                            @if($email->is_unlimited)
                                <div class="ae-meta-val" style="color:#34d399;">∞</div>
                            @else
                                <div class="ae-meta-val" style="{{ $email->stock > 0 ? 'color:white;' : 'color:#f87171;' }}">{{ $email->stock }}</div>
                            @endif
                        </div>
                        <div class="ae-meta-cell">
                            <div class="ae-meta-key">Asignaciones</div>
                            <div class="ae-meta-val" style="{{ $email->total_clients_count > 0 ? 'color:#fbbf24;' : 'color:#34d399;' }}">{{ $email->total_clients_count }}</div>
                        </div>
                    </div>
                </div>

                {{-- Foot: id + edit --}}
                <div class="ae-card-foot">
                    <span style="font-size:0.62rem;color:rgba(148,163,184,0.3);font-variant-numeric:tabular-nums;">#{{ $email->id }}</span>
                    <a wire:navigate href="{{ route('admin.allowed-emails.edit', $email) }}" class="ae-edit-btn">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </a>
                </div>

            </div>
            @endforeach
        </div>

        {{-- VISTA TABLA --}}
        @else
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;overflow:hidden;">
            <table class="ae-table">
                <thead>
                    <tr>
                        <th style="width:3rem;text-align:center;padding-left:1.25rem;">
                            <input type="checkbox" wire:model.live="selectAll" class="ae-check">
                        </th>
                        <th>Correo</th>
                        <th>Plataforma</th>
                        <th>Estado</th>
                        <th>Privacidad</th>
                        <th>Stock</th>
                        <th>Asignaciones</th>
                        <th style="text-align:right;padding-right:1.25rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allowedEmails as $email)
                    <tr wire:key="et-{{ $email->id }}" style="{{ in_array($email->id, $selectedIds) ? 'background:rgba(124,58,237,0.05);' : '' }}">
                        <td style="text-align:center;padding-left:1.25rem;">
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $email->id }}" class="ae-check">
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="ae-tbl-monogram">{{ strtoupper(substr($email->email, 0, 1)) }}</div>
                                <div>
                                    <div style="font-size:0.85rem;font-weight:700;color:white;">{{ $email->email }}</div>
                                    <div style="font-size:0.6rem;color:rgba(148,163,184,0.35);margin-top:0.15rem;">#{{ $email->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($email->platform)
                                <span class="ui-badge ui-badge--violet">{{ $email->platform->name }}</span>
                            @else
                                <span style="font-size:0.72rem;color:rgba(148,163,184,0.35);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($email->is_active)
                                <span class="ui-badge ui-badge--success">Activo</span>
                            @else
                                <span class="ui-badge ui-badge--error">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            @if($email->is_public)
                                <span class="ui-badge ui-badge--info">Público</span>
                            @else
                                <span class="ui-badge ui-badge--neutral">Privado</span>
                            @endif
                        </td>
                        <td>
                            @if($email->is_unlimited)
                                <span style="font-size:0.82rem;font-weight:800;color:#34d399;">∞ Ilimitado</span>
                            @else
                                <span style="font-size:0.82rem;font-weight:800;{{ $email->stock > 0 ? 'color:white;' : 'color:#f87171;' }}">{{ $email->stock }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.8rem;font-weight:700;">
                                @if($email->total_clients_count == 0)
                                    <span style="color:#34d399;">Libre</span>
                                @else
                                    <span style="color:#fbbf24;">{{ $email->total_clients_count }}</span>
                                    @if($email->active_clients_count > 0)<span style="color:rgba(148,163,184,0.4);font-weight:400;">·</span><span style="color:#60a5fa;">{{ $email->active_clients_count }} act.</span>@endif
                                    @if($email->expired_clients_count > 0)<span style="color:rgba(148,163,184,0.4);font-weight:400;">·</span><span style="color:#f87171;">{{ $email->expired_clients_count }} ven.</span>@endif
                                @endif
                            </div>
                        </td>
                        <td style="text-align:right;padding-right:1.25rem;">
                            <a wire:navigate href="{{ route('admin.allowed-emails.edit', $email) }}" class="ae-edit-btn">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @endif

        {{-- Paginación --}}
        @if($allowedEmails->hasPages())
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:0.875rem;padding:0.875rem 1.25rem;margin-top:0.5rem;">
            {{ $allowedEmails->links('pagination::tailwind', data: ['scrollTo' => false]) }}
        </div>
        @endif

    </div>

</div>