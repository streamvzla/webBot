@extends('admin.layouts.app')

@section('title', 'Dashboard - Panel de Administración')
@section('header', 'Dashboard')
@section('description', 'Resumen general del sistema y métricas clave')

@section('content')
    @if(isset($stats['franchises_expiring']) && $stats['franchises_expiring']->count() > 0)
        <div class="ae-card" style="margin-bottom: 2rem; border-color: rgba(245, 158, 11, 0.3); background: rgba(245, 158, 11, 0.05);">
            <div class="ae-card-head" style="border-bottom: 1px solid rgba(245, 158, 11, 0.1);">
                <div class="ae-card-title">
                    <div style="display:flex;align-items:center;gap:0.75rem; color:#f59e0b;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Franquicias por Vencer
                    </div>
                </div>
            </div>
            <div class="ae-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                    @foreach($stats['franchises_expiring'] as $franchise)
                        @php $days = $franchise->getDaysUntilExpiration(); @endphp
                        <div style="display:flex; justify-content:space-between; align-items:center; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div style="font-weight: 600; color: white;">{{ $franchise->name ?: $franchise->username }}</div>
                                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">{{ $franchise->email }}</div>
                            </div>
                            <div style="display:flex; align-items:center; gap:1rem;">
                                <span class="ui-badge" style="background:{{ $days <= 1 ? 'rgba(239,68,68,0.2)' : 'rgba(245,158,11,0.2)' }}; color:{{ $days <= 1 ? '#ef4444' : '#f59e0b' }}; border:none;">
                                    {{ $days <= 0 ? 'Vencido' : 'Vence en ' . $days . 'd' }}
                                </span>
                                <a href="{{ route('admin.users.edit', $franchise) }}" class="ui-btn ui-btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Ver</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <livewire:admin.dashboard-metrics />
@endsection
