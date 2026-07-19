@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-top:1.5rem;">

    {{-- Info de resultados --}}
    <div style="font-size:0.8rem;color:rgba(100,116,139,0.8);">
        Mostrando
        <span style="color:rgba(196,181,253,0.9);font-weight:600;">{{ $paginator->firstItem() }}</span>
        al
        <span style="color:rgba(196,181,253,0.9);font-weight:600;">{{ $paginator->lastItem() }}</span>
        de
        <span style="color:white;font-weight:600;">{{ $paginator->total() }}</span>
        resultados
    </div>

    {{-- Botones --}}
    <div style="display:flex;align-items:center;gap:0.375rem;">

        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:rgba(100,116,139,0.4);cursor:not-allowed;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:rgba(168,85,247,0.08);border:1px solid rgba(168,85,247,0.2);color:#c4b5fd;transition:all 0.2s;text-decoration:none;" onmouseover="this.style.background='rgba(168,85,247,0.18)';this.style.borderColor='rgba(168,85,247,0.4)'" onmouseout="this.style.background='rgba(168,85,247,0.08)';this.style.borderColor='rgba(168,85,247,0.2)'">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Números de página --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:transparent;color:rgba(100,116,139,0.6);font-size:0.875rem;">···</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" style="display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:linear-gradient(135deg,#7c3aed,#a855f7,#ec4899);color:white;font-size:0.8rem;font-weight:700;box-shadow:0 4px 15px rgba(168,85,247,0.35);border:none;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);color:rgba(148,163,184,0.8);font-size:0.8rem;font-weight:500;transition:all 0.2s;text-decoration:none;" onmouseover="this.style.background='rgba(168,85,247,0.1)';this.style.borderColor='rgba(168,85,247,0.25)';this.style.color='#c4b5fd'" onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.borderColor='rgba(255,255,255,0.07)';this.style.color='rgba(148,163,184,0.8)'">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:rgba(168,85,247,0.08);border:1px solid rgba(168,85,247,0.2);color:#c4b5fd;transition:all 0.2s;text-decoration:none;" onmouseover="this.style.background='rgba(168,85,247,0.18)';this.style.borderColor='rgba(168,85,247,0.4)'" onmouseout="this.style.background='rgba(168,85,247,0.08)';this.style.borderColor='rgba(168,85,247,0.2)'">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span style="display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;border-radius:0.625rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:rgba(100,116,139,0.4);cursor:not-allowed;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

    </div>
</nav>
@endif
