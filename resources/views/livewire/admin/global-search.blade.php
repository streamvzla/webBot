<div class="relative w-full max-w-md" x-data="{ isOpen: @entangle('isOpen') }" @click.away="isOpen = false">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            @focus="isOpen = true"
            @keydown.escape="isOpen = false"
            class="w-full bg-slate-800/50 border border-slate-700/50 text-slate-300 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 transition-all placeholder-slate-500 text-sm"
            placeholder="Buscar clientes o correos... (Ctrl+K)"
            x-ref="searchInput"
        >
        
        <!-- Ctrl+K shortcut listener -->
        <div x-data @keydown.window.prevent.ctrl.k="$refs.searchInput.focus()"></div>
        <div x-data @keydown.window.prevent.meta.k="$refs.searchInput.focus()"></div>
        
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <span class="text-[10px] text-slate-500 font-medium bg-slate-800 border border-slate-700 px-1.5 py-0.5 rounded">Ctrl K</span>
        </div>
    </div>

    <!-- Resultados -->
    <div 
        x-show="isOpen && $wire.search.length >= 2"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute top-full left-0 right-0 mt-2 bg-slate-900 border border-slate-700/50 rounded-xl shadow-2xl overflow-hidden z-50 max-h-96 overflow-y-auto"
        style="display: none;"
    >
        @if(count($results) > 0)
            <div class="p-2">
                @foreach($results as $result)
                    <a href="{{ $result['url'] }}" wire:navigate class="flex items-center gap-4 p-3 hover:bg-slate-800 rounded-lg transition group">
                        <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center group-hover:bg-slate-700 transition">
                            {!! $result['icon'] !!}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-500 font-medium uppercase tracking-wider">{{ $result['type'] }}</span>
                            <span class="text-sm font-semibold text-slate-200 group-hover:text-blue-400 transition">{{ $result['title'] }}</span>
                            <span class="text-xs text-slate-400">{{ $result['subtitle'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-slate-500 flex flex-col items-center">
                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm">No se encontraron resultados</span>
            </div>
        @endif
    </div>
</div>