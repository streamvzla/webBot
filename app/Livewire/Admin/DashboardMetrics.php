<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Query;
use App\Models\User;
use App\Models\Client;
use App\Models\AllowedEmail;
use App\Models\IpBan;
use App\Models\Platform;
use Carbon\Carbon;

class DashboardMetrics extends Component
{
    public $filter       = 'all';
    public $search       = '';    // búsqueda en tiempo real
    public $limitQueries = 8;    // paginación livewire

    // ── Helper Multi-Tenancy ─────────────────────────────────────────────────
    private function scopeModel($modelClass)
    {
        $q = $modelClass::query();
        if (auth()->id() !== 1) {
            $descendants = auth()->user()->getDescendantsIds();
            $table = (new $modelClass)->getTable();
            if ($modelClass === User::class) {
                $q->whereIn($table . '.id', $descendants);
            } elseif ($modelClass === IpBan::class) {
                // Admins do not see IP bans
                $q->whereRaw('1 = 0');
            } else {
                $q->whereIn($table . '.user_id', $descendants);
            }
        }
        return $q;
    }

    // ── Computed: Métricas principales ───────────────────────────────────────
    public function getMetricsProperty()
    {
        $q     = $this->baseQuery();
        $qPrev = $this->prevQuery();

        $total     = $q->count();
        $prevTotal = $qPrev->count();
        $delta     = $prevTotal > 0 ? round((($total - $prevTotal) / $prevTotal) * 100, 1) : 0;

        $totalClients  = $this->scopeModel(Client::class)->count();
        $activeClients = $this->scopeModel(Client::class)->where('is_active', true)->count();

        // Clientes con suscripción vencida
        $expiredCount = $this->scopeModel(Client::class)->whereHas('allowedEmails', fn($q) =>
            $q->whereNotNull('allowed_email_client.expires_at')
              ->whereDate('allowed_email_client.expires_at', '<', today())
        )->count();

        return [
            'totalQueries'   => $total,
            'delta'          => $delta,
            'totalClients'   => $totalClients,
            'activeClients'  => $activeClients,
            'inactiveClients'=> $totalClients - $activeClients,
            'totalEmails'    => $this->scopeModel(AllowedEmail::class)->count(),
            'totalUsers'     => $this->scopeModel(User::class)->count(),
            'totalPlatforms' => class_exists(Platform::class) ? $this->scopeModel(Platform::class)->count() : 0,
            'totalBans'      => $this->scopeModel(IpBan::class)->count(),
            'todayCount'     => $this->baseQuery()->whereDate('created_at', today())->count(),
            'yesterdayCount' => $this->baseQuery()->whereDate('created_at', today()->subDay())->count(),
            'expiredCount'   => $expiredCount,
        ];
    }

    // ── Computed: Consultas recientes (reactivo a $search) ──────────────────
    public function getRecentQueriesProperty()
    {
        return $this->baseQuery()
            ->with(['platform', 'client'])
            ->when($this->search, fn($q) =>
                $q->where(fn($sq) =>
                    $sq->where('email', 'like', '%'.$this->search.'%')
                       ->orWhereHas('client', fn($cq) => $cq->where('name','like','%'.$this->search.'%'))
                       ->orWhereHas('platform', fn($pq) => $pq->where('name','like','%'.$this->search.'%'))
                )
            )
            ->orderBy('created_at', 'desc')
            ->limit($this->limitQueries)
            ->get();
    }

    // ── Computed: Top 5 clientes más activos ─────────────────────────────────
    public function getTopClientsProperty()
    {
        return $this->scopeModel(Client::class)
            ->select('clients.*')
            ->selectRaw('COUNT(queries.id) as query_count')
            ->leftJoin('queries', 'queries.client_id', '=', 'clients.id')
            ->groupBy('clients.id')
            ->orderByDesc('query_count')
            ->limit(5)
            ->get();
    }

    // ── Computed: Clientes nuevos por mes (últimos 6 meses) ──────────────────
    public function getMonthlyClientsProperty()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = $this->scopeModel(Client::class)
                           ->whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->count();
            $months->push([
                'label' => $date->locale('es')->isoFormat('MMM'),
                'count' => $count,
            ]);
        }
        return $months;
    }

    // ── Computed: Actividad por hora (heatmap) ───────────────────────────────
    public function getHeatmapProperty()
    {
        $rows = $this->baseQuery()
            ->selectRaw("CAST(strftime('%H', created_at) AS INTEGER) as hour, COUNT(*) as total")
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        $max = max($rows + [0 => 0]);
        $data = [];
        for ($h = 0; $h < 24; $h++) {
            $val       = $rows[$h] ?? 0;
            $intensity = $max > 0 ? $val / $max : 0;
            $data[]    = ['hour' => $h, 'count' => $val, 'intensity' => $intensity];
        }
        return $data;
    }

    // ── Computed: Consultas por hora (gráfica) ───────────────────────────
    public function getChartDataProperty()
    {
        $rows = $this->baseQuery()
            ->selectRaw("CAST(strftime('%H', created_at) AS INTEGER) as hour, COUNT(*) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        $labels = [];
        $values = [];
        for ($h = 0; $h < 24; $h++) {
            $labels[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $values[] = $rows[$h] ?? 0;
        }
        return ['labels' => $labels, 'values' => $values];
    }

    // ── Computed: Clientes activos vs inactivos ──────────────────────────────
    public function getClientStatsProperty()
    {
        $total    = $this->scopeModel(Client::class)->count();
        $active   = $this->scopeModel(Client::class)->where('is_active', true)->count();
        $inactive = $total - $active;
        $pct      = $total > 0 ? round(($active / $total) * 100) : 0;
        return compact('total', 'active', 'inactive', 'pct');
    }

    // ── Computed: Renovaciones próximas ──────────────────────────────────────
    public function getExpiringClientsProperty()
    {
        return $this->scopeModel(Client::class)->whereHas('allowedEmails', fn($q) =>
            $q->whereNotNull('allowed_email_client.expires_at')
              ->whereDate('allowed_email_client.expires_at', '>=', now()->toDateString())
              ->whereDate('allowed_email_client.expires_at', '<=', now()->addDays(7)->toDateString())
        )->with(['allowedEmails' => fn($q) =>
            $q->whereNotNull('allowed_email_client.expires_at')
              ->orderBy('allowed_email_client.expires_at', 'asc')
        ])->take(5)->get();
    }

    // ── Computed: IPs baneadas recientes ────────────────────────────────────
    public function getSecurityThreatsProperty()
    {
        return $this->scopeModel(IpBan::class)->orderBy('created_at', 'desc')->take(6)->get();
    }

    // ── Acción: Renovar TODOS los vencidos ──────────────────────────────────
    public function renewAllExpired()
    {
        $clients = $this->scopeModel(Client::class)->whereHas('allowedEmails', fn($q) =>
            $q->whereNotNull('allowed_email_client.expires_at')
              ->whereDate('allowed_email_client.expires_at', '<', today())
        )->with('allowedEmails')->get();

        foreach ($clients as $client) {
            foreach ($client->allowedEmails as $email) {
                $client->allowedEmails()->updateExistingPivot($email->id, [
                    'expires_at' => now()->addDays(30),
                ]);
            }
        }

        $this->dispatch('notif', message: "✅ {$clients->count()} clientes renovados +30 días.");
    }

    // ── Acción: Desbanear IP ─────────────────────────────────────────────────
    public function unbanIp($id)
    {
        $ban = $this->scopeModel(IpBan::class)->findOrFail($id);
        $ban->delete();
        $this->dispatch('notif', message: '🛡️ IP desbaneada correctamente.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    private function baseQuery()
    {
        $q = Query::query();
        if (auth()->id() !== 1) {
            $q->whereIn('user_id', auth()->user()->getDescendantsIds());
        }
        if ($this->filter === 'today') $q->whereDate('created_at', today());
        if ($this->filter === 'week')  $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        if ($this->filter === 'month') $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        return $q;
    }

    private function prevQuery()
    {
        $q = Query::query();
        if (auth()->id() !== 1) {
            $q->whereIn('user_id', auth()->user()->getDescendantsIds());
        }
        if ($this->filter === 'today') $q->whereDate('created_at', today()->subDay());
        if ($this->filter === 'week')  $q->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
        if ($this->filter === 'month') $q->whereMonth('created_at', now()->subMonth()->month);
        return $q;
    }

    public function updatedFilter()
    {
        $this->dispatch('filter-updated', chartData: $this->chartData);
    }

    public function render()
    {
        return view('livewire.admin.dashboard-metrics');
    }
}
