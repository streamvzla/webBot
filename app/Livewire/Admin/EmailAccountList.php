<?php

namespace App\Livewire\Admin;

use App\Models\EmailAccount;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class EmailAccountList extends Component
{
    use WithPagination;

    public string $search     = '';
    public string $status     = '';
    public string $assigned   = '';
    public string $sortBy     = 'email';
    public string $sortDir    = 'asc';
    public string $view       = 'cards';

    public ?int $drawerAccountId  = null;
    public ?int $confirmDeleteId  = null;

    protected $queryString = [
        'search'   => ['except' => ''],
        'status'   => ['except' => ''],
        'assigned' => ['except' => ''],
        'sortBy'   => ['except' => 'email'],
        'sortDir'  => ['except' => 'asc'],
        'view'     => ['except' => 'cards'],
    ];

    public function updatingSearch()  { $this->resetPage(); }
    public function updatingStatus()  { $this->resetPage(); }
    public function updatingAssigned(){ $this->resetPage(); }

    public function sortBy(string $col): void
    {
        $this->sortDir = $this->sortBy === $col
            ? ($this->sortDir === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortBy = $col;
        $this->resetPage();
    }

    // ── Stats ──────────────────────────────────────────────────────────────────
    public function getStatsProperty(): array
    {
        $base = EmailAccount::where('user_id', auth()->id());
        return [
            'total'      => (clone $base)->count(),
            'active'     => (clone $base)->where('is_active', true)->count(),
            'inactive'   => (clone $base)->where('is_active', false)->count(),
            'assigned'   => (clone $base)->has('users')->count(),
            'unassigned' => (clone $base)->doesntHave('users')->count(),
        ];
    }

    // ── Paginated list ─────────────────────────────────────────────────────────
    public function getAccountsProperty()
    {
        return EmailAccount::where('user_id', auth()->id())->with('users')
            ->when($this->search, fn($q, $s) => $q->where(fn($sq) =>
                $sq->where('email',     'like', "%{$s}%")
                   ->orWhere('username','like', "%{$s}%")
                   ->orWhere('imap_host','like',"%{$s}%")
            ))
            ->when($this->status !== '',   fn($q) => $q->where('is_active', (bool)$this->status))
            ->when($this->assigned === '1', fn($q) => $q->has('users'))
            ->when($this->assigned === '0', fn($q) => $q->doesntHave('users'))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(12);
    }

    // ── Drawer ─────────────────────────────────────────────────────────────────
    public function openDrawer(int $id): void  { $this->drawerAccountId = $id; }
    public function closeDrawer(): void        { $this->drawerAccountId = null; }

    public function getDrawerAccountProperty(): ?EmailAccount
    {
        return $this->drawerAccountId
            ? EmailAccount::with('users')->find($this->drawerAccountId)
            : null;
    }

    // ── Toggle active ──────────────────────────────────────────────────────────
    public function toggleActive(int $id): void
    {
        $acc = EmailAccount::findOrFail($id);
        if ($acc->user_id !== auth()->id()) abort(403);
        $acc->is_active = !$acc->is_active;
        $acc->save();
        $this->dispatch('notif', message: $acc->is_active ? '✅ Cuenta activada' : '⏸ Cuenta desactivada');
    }

    // ── Delete ─────────────────────────────────────────────────────────────────
    public function confirmDelete(int $id): void { $this->confirmDeleteId = $id; }
    public function cancelDelete(): void         { $this->confirmDeleteId = null; }

    public function deleteAccount(): void
    {
        if (!$this->confirmDeleteId) return;
        $acc = EmailAccount::findOrFail($this->confirmDeleteId);
        if ($acc->user_id !== auth()->id()) abort(403);
        $acc->users()->detach();
        $acc->delete();
        $this->confirmDeleteId = null;
        if ($this->drawerAccountId) $this->drawerAccountId = null;
        $this->dispatch('notif', message: '🗑 Cuenta eliminada');
    }

    // ── Provider helper (reuse ServerList logic) ───────────────────────────────
    public static function detectProvider(string $host): array
    {
        $map = [
            'gmail'      => ['name' => 'Gmail',         'color' => '#EA4335', 'icon' => 'G'],
            'googlemail' => ['name' => 'Gmail',         'color' => '#EA4335', 'icon' => 'G'],
            'outlook'    => ['name' => 'Outlook',       'color' => '#0078D4', 'icon' => 'O'],
            'office365'  => ['name' => 'Microsoft 365', 'color' => '#0078D4', 'icon' => 'M'],
            'yahoo'      => ['name' => 'Yahoo',         'color' => '#6001D2', 'icon' => 'Y'],
            'zoho'       => ['name' => 'Zoho',          'color' => '#E42527', 'icon' => 'Z'],
            'icloud'     => ['name' => 'iCloud',        'color' => '#007AFF', 'icon' => ''],
            'aol'        => ['name' => 'AOL',           'color' => '#FF0B00', 'icon' => 'A'],
            'yandex'     => ['name' => 'Yandex',        'color' => '#FC3F1D', 'icon' => 'Я'],
            'gmx'        => ['name' => 'GMX',           'color' => '#1D449B', 'icon' => 'G'],
            'titan'      => ['name' => 'Titan',         'color' => '#0F4C81', 'icon' => 'T'],
            'proton'     => ['name' => 'ProtonMail',    'color' => '#6D4AFF', 'icon' => 'P'],
            'hostinger'  => ['name' => 'Hostinger',     'color' => '#673de6', 'icon' => 'H'],
        ];
        foreach ($map as $keyword => $info) {
            if (str_contains(strtolower($host), $keyword)) return $info;
        }
        return ['name' => 'Personalizado', 'color' => '#7c3aed', 'icon' => '⚙'];
    }

    public function render()
    {
        return view('livewire.admin.email-account-list', [
            'accounts' => $this->accounts,
            'stats'    => $this->stats,
        ])->extends('admin.layouts.app')->section('content');
    }
}
