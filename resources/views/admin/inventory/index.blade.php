@extends('admin.layouts.app')

@section('title', 'Mi Inventario - Panel de Administración')
@section('header', 'Mi Inventario')
@section('description', 'Gestiona tus cuentas asignadas y renuévalas antes de que expiren')

@section('content')
<style>
/* ── BULK ACTION BAR ── */
.ui-bulk-bar { position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%) translateY(100px);opacity:0;background:rgba(15,20,50,0.95);border:1px solid rgba(168,85,247,0.4);backdrop-filter:blur(12px);border-radius:1.5rem;padding:0.75rem 1.5rem;display:flex;align-items:center;gap:1.5rem;box-shadow:0 10px 40px rgba(168,85,247,0.25);z-index:999;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.ui-bulk-bar.visible { transform:translateX(-50%) translateY(0);opacity:1; }
.ui-bulk-btn { display:inline-flex;align-items:center;justify-content:center;height:2.25rem;padding:0 1rem;gap:0.5rem;border-radius:0.625rem;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.7);transition:all 0.2s;cursor:pointer;border:none;font-size:0.85rem;font-weight:700; }
.ui-bulk-btn:hover { transform:translateY(-2px);color:white; }
.ui-bulk-btn.emerald:hover { background:rgba(16,185,129,0.2);color:#34d399; }
.ui-bulk-btn.rose:hover    { background:rgba(239,68,68,0.2);color:#f87171; }
</style>

<div class="max-w-7xl mx-auto space-y-8">

    {{-- ── HERO ── --}}
    <div class="ui-hero ui-anim-in">
        <div>
            <div class="ui-hero-tag">
                Administración de Cuentas
            </div>
            <h1 class="ui-hero-title">Mi Inventario</h1>
            <p class="ui-hero-sub">Gestiona tus cuentas asignadas y renuévalas antes de que expiren.</p>
        </div>
    </div>

    {{-- ── STATS GRID ── --}}
    <div style="display:flex;align-items:stretch;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;" class="ui-anim-in ui-delay-1">
        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(168,85,247,0.08);">
                <svg width="18" height="18" fill="none" stroke="rgba(168,85,247,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#c084fc;">{{ $stats['total'] }}</div>
                <div class="ae-metric-label">Total en Inventario</div>
            </div>
        </div>

        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(16,185,129,0.08);">
                <svg width="18" height="18" fill="none" stroke="rgba(16,185,129,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#34d399;">{{ $stats['free'] }}</div>
                <div class="ae-metric-label">Libres (Stock)</div>
            </div>
        </div>

        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(59,130,246,0.08);">
                <svg width="18" height="18" fill="none" stroke="rgba(59,130,246,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#60a5fa;">{{ $stats['assigned'] }}</div>
                <div class="ae-metric-label">Ocupadas (Vendidas)</div>
            </div>
        </div>

        <div class="ae-metric" style="flex:1;">
            <div class="ae-metric-icon" style="background:rgba(239,68,68,0.08);">
                <svg width="18" height="18" fill="none" stroke="rgba(239,68,68,0.7)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ae-metric-num" style="color:#fca5a5;">{{ $stats['expired'] }}</div>
                <div class="ae-metric-label">Vencidas</div>
            </div>
        </div>
    </div>

    {{-- ── MAIN CARD ── --}}
    <div class="ae-card ui-anim-in ui-delay-2">
        <div class="ae-card-head">
            <div class="ae-card-title">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="ui-icon-wrap">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    Gestión de Renovaciones
                </div>
            </div>
        </div>

        <div class="ae-card-body">
            
            {{-- TOOLBAR FILTROS --}}
            <div class="ae-toolbar" style="margin-bottom: 1.5rem; justify-content: flex-start; overflow-x: auto;">
                <div style="display: flex; flex-wrap: nowrap; gap: 0.5rem;">
                    <a href="{{ route('admin.inventory.index') }}" class="ui-filter-pill {{ !request('expiration') && !request('status') ? 'active' : '' }}">Todas</a>
                    <a href="{{ route('admin.inventory.index', ['status' => 'free']) }}" class="ui-filter-pill {{ request('status') === 'free' ? 'active' : '' }}">Libres</a>
                    <a href="{{ route('admin.inventory.index', ['status' => 'assigned']) }}" class="ui-filter-pill {{ request('status') === 'assigned' ? 'active' : '' }}">Ocupadas</a>
                    
                    <div style="width:1px;height:1.5rem;background:rgba(255,255,255,0.1);margin:0 0.5rem;align-self:center;"></div>
                    
                    <a href="{{ route('admin.inventory.index', ['expiration' => 'expired']) }}" class="ui-filter-pill {{ request('expiration') === 'expired' ? 'active' : '' }}" style="{{ request('expiration') === 'expired' ? 'border-color: rgba(239,68,68,0.5); background: rgba(239,68,68,0.1); color: #fca5a5;' : 'color: #fca5a5;' }}">🔴 Vencidas</a>
                    <a href="{{ route('admin.inventory.index', ['expiration' => '1_day']) }}" class="ui-filter-pill {{ request('expiration') === '1_day' ? 'active' : '' }}" style="{{ request('expiration') === '1_day' ? 'border-color: rgba(245,158,11,0.5); background: rgba(245,158,11,0.1); color: #fcd34d;' : 'color: #fcd34d;' }}">🟠 Vencen Mañana</a>
                    <a href="{{ route('admin.inventory.index', ['expiration' => '2_days']) }}" class="ui-filter-pill {{ request('expiration') === '2_days' ? 'active' : '' }}">🟡 Vencen en 2 días</a>
                    <a href="{{ route('admin.inventory.index', ['expiration' => '3_days']) }}" class="ui-filter-pill {{ request('expiration') === '3_days' ? 'active' : '' }}">🟡 Vencen en 3 días</a>
                </div>
            </div>

            {{-- TABLA --}}
            <div style="overflow-x:auto;">
                <table class="ae-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="selectAll" style="accent-color:#a855f7;width:1rem;height:1rem;cursor:pointer;" onchange="toggleAll(this)">
                            </th>
                            <th>Cuenta / Plataforma</th>
                            <th>Estado Cliente</th>
                            <th>F. Compra</th>
                            <th>Vencimiento (Tuyo)</th>
                            <th>Tiempo Restante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                            @php
                                $isExpired = $item->expires_at && $item->expires_at->isPast();
                                $daysLeft = $item->expires_at ? now()->startOfDay()->diffInDays($item->expires_at->startOfDay(), false) : null;
                                $hasActiveClient = $item->clients->count() > 0;
                            @endphp
                            <tr class="{{ $isExpired ? 'bg-[rgba(239,68,68,0.02)]' : '' }}">
                                <td style="text-align: center;">
                                    <input type="checkbox" class="row-checkbox" value="{{ $item->id }}" data-email="{{ $item->email }}" style="accent-color:#a855f7;width:1rem;height:1rem;cursor:pointer;" onchange="updateFloatingBar()">
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:1rem;">
                                        @if($item->platform && $item->platform->logo)
                                            <img src="{{ asset('storage/' . $item->platform->logo) }}" alt="{{ $item->platform->name }}" style="width:2.25rem; height:2.25rem; border-radius:0.5rem; object-fit:cover; border:1px solid rgba(255,255,255,0.1);">
                                        @else
                                            <div class="ae-monogram">
                                                {{ substr($item->platform->name ?? '?', 0, 2) }}
                                            </div>
                                        @endif
                                        <div style="display:flex; flex-direction:column;">
                                            <span style="font-weight:800; color:white; font-size:0.95rem; letter-spacing:0.02em;">{{ $item->email }}</span>
                                            <span style="font-size:0.75rem; color:rgba(148,163,184,0.7); font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-top:0.2rem;">{{ $item->platform->name ?? 'Sin Plataforma' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($hasActiveClient)
                                        <span class="ui-badge-neon success">Vendida</span>
                                    @else
                                        <span class="ui-badge-neon" style="color:#60a5fa;border-color:rgba(96,165,250,0.2);background:rgba(96,165,250,0.05);">Stock Libre</span>
                                    @endif
                                </td>
                                <td style="color: rgba(148,163,184,0.9); font-weight:500;">
                                    {{ $item->created_at->format('d M, Y') }}
                                </td>
                                <td>
                                    @if($item->expires_at)
                                        <span style="color: {{ $isExpired ? '#fca5a5' : 'white' }}; font-weight: {{ $isExpired ? '800' : '600' }};">
                                            {{ $item->expires_at->format('d M, Y') }}
                                        </span>
                                    @else
                                        <span style="color: rgba(148,163,184,0.5); font-weight:500; font-style:italic;">Sin fecha</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->expires_at)
                                        @if($daysLeft < 0)
                                            <span class="ui-badge-neon error" style="display:inline-flex;align-items:center;gap:0.3rem;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Vencida ({{ abs($daysLeft) }} d)</span>
                                        @elseif($daysLeft == 0)
                                            <span class="ui-badge-neon warning" style="display:inline-flex;align-items:center;gap:0.3rem;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Vence HOY</span>
                                        @elseif($daysLeft <= 3)
                                            <span class="ui-badge-neon warning">Quedan {{ $daysLeft }} días</span>
                                        @else
                                            <span class="ui-badge-neon success">{{ $daysLeft }} días libres</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 4rem 2rem; text-align: center;">
                                    <div style="width:3rem;height:3rem;border-radius:0.75rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                                        <svg width="20" height="20" fill="none" stroke="rgba(148,163,184,0.4)" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p style="font-size:0.95rem;font-weight:700;color:rgba(241,245,249,0.7);margin-bottom:0.4rem;">Sin Resultados</p>
                                    <p style="font-size:0.8rem;color:rgba(148,163,184,0.4);">No se encontraron cuentas en el inventario con los filtros actuales.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding: 1rem;">
                {{ $inventory->appends(request()->query())->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>

{{-- ── FLOATING ACTION BAR PARA SELECCION MULTIPLE (BULK BAR) ── --}}
<div class="ui-bulk-bar" id="floatingBar">
    <div style="display:flex; align-items:center; gap:0.75rem;">
        <span id="selCount" style="background:var(--ui-gradient, linear-gradient(135deg,#a855f7,#ec4899));width:1.5rem;height:1.5rem;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:0.75rem;font-weight:700;color:white;">0</span>
        <span style="color:white;font-weight:700;font-size:0.9rem;">cuentas seleccionadas</span>
    </div>
    
    <div style="width:1px;height:1.5rem;background:rgba(255,255,255,0.1);"></div>
    
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <button type="button" class="ui-bulk-btn emerald" onclick="sendWhatsApp()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
        Renovar (WhatsApp)
        </button>
        <button type="button" class="ui-bulk-btn rose" onclick="releaseAccounts()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Devolver
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAll(source) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateFloatingBar();
    }

    function updateFloatingBar() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkedBoxes.length;
        
        document.getElementById('selCount').innerText = count;
        
        const fab = document.getElementById('floatingBar');
        if (count > 0) {
            fab.classList.add('visible');
        } else {
            fab.classList.remove('visible');
            document.getElementById('selectAll').checked = false;
        }
    }

    function getSelectedEmails() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        return Array.from(checkedBoxes).map(cb => cb.getAttribute('data-email'));
    }

    function getSelectedIds() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        return Array.from(checkedBoxes).map(cb => cb.value);
    }

    function sendWhatsApp() {
        const emails = getSelectedEmails();
        if(emails.length === 0) return;

        let message = "Hola, solicito la renovación de las siguientes cuentas de mi inventario:\n\n";
        emails.forEach((email, index) => {
            message += `${index + 1}. ${email}\n`;
        });
        
        const adminPhone = "{{ \App\Models\Setting::get('whatsapp_number', '') }}"; 
        
        const encodedMessage = encodeURIComponent(message);
        const waLink = adminPhone ? `https://wa.me/${adminPhone}?text=${encodedMessage}` : `https://wa.me/?text=${encodedMessage}`;
        
        window.open(waLink, '_blank');
    }

    function releaseAccounts() {
        const ids = getSelectedIds();
        if(ids.length === 0) return;

        if(!confirm(`¿Estás seguro que deseas devolver estas ${ids.length} cuentas al Administrador?\n\nAl confirmar, las cuentas y sus clientes asociados desaparecerán de tu inventario.`)) {
            return;
        }

        fetch('{{ route('admin.inventory.release') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email_ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Error al liberar las cuentas.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al liberar cuentas.');
        });
    }
</script>
@endpush