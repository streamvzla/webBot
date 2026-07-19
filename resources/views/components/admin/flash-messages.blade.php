{{--
    Flash Messages / Toast Notifications Premium
    Uso: @include('components.admin.flash-messages') o <x-admin.flash-messages />
    Soporta: success, error, warning, info
--}}

@if(session()->hasAny(['success', 'error', 'warning', 'info']))
<div id="flash-container" style="
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    pointer-events: none;
    max-width: 22rem;
    width: calc(100vw - 3rem);
">

@if(session('success'))
<div class="flash-toast flash-success" style="
    pointer-events: all;
    background: linear-gradient(135deg, rgba(15,10,40,0.97) 0%, rgba(10,5,30,0.98) 100%);
    border: 1px solid rgba(52,211,153,0.3);
    border-left: 3px solid #34d399;
    border-radius: 0.875rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5), 0 0 0 1px rgba(52,211,153,0.05), 0 0 20px rgba(52,211,153,0.08);
    animation: slideInRight 0.35s cubic-bezier(0.16,1,0.3,1) both;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(52,211,153,0.4),transparent);"></div>
    <div style="width:2.25rem;height:2.25rem;background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.25);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg style="width:1.1rem;height:1.1rem;color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div style="flex:1;min-width:0;">
        <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#34d399;margin-bottom:0.2rem;">Éxito</p>
        <p style="font-size:0.875rem;color:rgba(226,232,240,0.9);line-height:1.4;">{{ session('success') }}</p>
    </div>
    <button onclick="this.closest('.flash-toast').remove()" style="color:rgba(100,116,139,0.5);background:none;border:none;cursor:pointer;padding:0.1rem;flex-shrink:0;transition:color 0.2s;" onmouseover="this.style.color='rgba(148,163,184,0.9)'" onmouseout="this.style.color='rgba(100,116,139,0.5)'">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

@if(session('error'))
<div class="flash-toast flash-error" style="
    pointer-events: all;
    background: linear-gradient(135deg, rgba(15,10,40,0.97) 0%, rgba(10,5,30,0.98) 100%);
    border: 1px solid rgba(239,68,68,0.3);
    border-left: 3px solid #ef4444;
    border-radius: 0.875rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5), 0 0 0 1px rgba(239,68,68,0.05), 0 0 20px rgba(239,68,68,0.08);
    animation: slideInRight 0.35s cubic-bezier(0.16,1,0.3,1) both;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(239,68,68,0.4),transparent);"></div>
    <div style="width:2.25rem;height:2.25rem;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg style="width:1.1rem;height:1.1rem;color:#f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div style="flex:1;min-width:0;">
        <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#f87171;margin-bottom:0.2rem;">Error</p>
        <p style="font-size:0.875rem;color:rgba(226,232,240,0.9);line-height:1.4;">{{ session('error') }}</p>
    </div>
    <button onclick="this.closest('.flash-toast').remove()" style="color:rgba(100,116,139,0.5);background:none;border:none;cursor:pointer;padding:0.1rem;flex-shrink:0;transition:color 0.2s;" onmouseover="this.style.color='rgba(148,163,184,0.9)'" onmouseout="this.style.color='rgba(100,116,139,0.5)'">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

@if(session('warning'))
<div class="flash-toast flash-warning" style="
    pointer-events: all;
    background: linear-gradient(135deg, rgba(15,10,40,0.97) 0%, rgba(10,5,30,0.98) 100%);
    border: 1px solid rgba(251,191,36,0.3);
    border-left: 3px solid #fbbf24;
    border-radius: 0.875rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5), 0 0 0 1px rgba(251,191,36,0.05), 0 0 20px rgba(251,191,36,0.08);
    animation: slideInRight 0.35s cubic-bezier(0.16,1,0.3,1) both;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(251,191,36,0.4),transparent);"></div>
    <div style="width:2.25rem;height:2.25rem;background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.25);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg style="width:1.1rem;height:1.1rem;color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <div style="flex:1;min-width:0;">
        <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#fbbf24;margin-bottom:0.2rem;">Atención</p>
        <p style="font-size:0.875rem;color:rgba(226,232,240,0.9);line-height:1.4;">{{ session('warning') }}</p>
    </div>
    <button onclick="this.closest('.flash-toast').remove()" style="color:rgba(100,116,139,0.5);background:none;border:none;cursor:pointer;padding:0.1rem;flex-shrink:0;transition:color 0.2s;" onmouseover="this.style.color='rgba(148,163,184,0.9)'" onmouseout="this.style.color='rgba(100,116,139,0.5)'">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

@if(session('info'))
<div class="flash-toast flash-info" style="
    pointer-events: all;
    background: linear-gradient(135deg, rgba(15,10,40,0.97) 0%, rgba(10,5,30,0.98) 100%);
    border: 1px solid rgba(168,85,247,0.3);
    border-left: 3px solid #a855f7;
    border-radius: 0.875rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5), 0 0 0 1px rgba(168,85,247,0.05), 0 0 20px rgba(168,85,247,0.08);
    animation: slideInRight 0.35s cubic-bezier(0.16,1,0.3,1) both;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(168,85,247,0.5),rgba(236,72,153,0.3),transparent);"></div>
    <div style="width:2.25rem;height:2.25rem;background:rgba(168,85,247,0.1);border:1px solid rgba(168,85,247,0.25);border-radius:0.625rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg style="width:1.1rem;height:1.1rem;color:#a855f7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div style="flex:1;min-width:0;">
        <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#a855f7;margin-bottom:0.2rem;">Info</p>
        <p style="font-size:0.875rem;color:rgba(226,232,240,0.9);line-height:1.4;">{{ session('info') }}</p>
    </div>
    <button onclick="this.closest('.flash-toast').remove()" style="color:rgba(100,116,139,0.5);background:none;border:none;cursor:pointer;padding:0.1rem;flex-shrink:0;transition:color 0.2s;" onmouseover="this.style.color='rgba(148,163,184,0.9)'" onmouseout="this.style.color='rgba(100,116,139,0.5)'">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

</div>

<style>
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(2rem) scale(0.95); }
    to   { opacity: 1; transform: translateX(0) scale(1); }
}
@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to   { opacity: 0; transform: scale(0.95) translateX(1rem); }
}
.flash-toast.removing {
    animation: fadeOut 0.3s ease forwards;
}
</style>

<script>
(function() {
    // Auto-dismiss after 5s
    setTimeout(function() {
        document.querySelectorAll('.flash-toast').forEach(function(el) {
            el.classList.add('removing');
            setTimeout(function() { el.remove(); }, 300);
        });
    }, 5000);
})();
</script>
@endif
