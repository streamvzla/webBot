<?php

namespace App\Livewire\Admin;

use App\Models\EmailAccount;
use App\Services\ImapConnector;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class ServerList extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $search    = '';
    public string $status    = '';
    public string $authorized = '';
    public string $sortBy    = 'email';
    public string $sortDir   = 'asc';
    public string $view      = 'cards'; // cards | table

    // ── Quick Test state ─────────────────────────────────────────────────────
    public ?int   $testingId      = null;
    public ?string $testResult    = null; // null | success | error
    public string  $testMessage   = '';
    public array   $testData      = [];

    // ── Drawer / Detail Panel ────────────────────────────────────────────────
    public ?int   $drawerServerId = null;

    // ── Delete confirm ───────────────────────────────────────────────────────
    public ?int $confirmDeleteId = null;

    // ── URL Query String ─────────────────────────────────────────────────────
    protected $queryString = [
        'search'     => ['except' => ''],
        'status'     => ['except' => ''],
        'authorized' => ['except' => ''],
        'sortBy'     => ['except' => 'email'],
        'sortDir'    => ['except' => 'asc'],
        'view'       => ['except' => 'cards'],
    ];



    // ── Updaters ──────────────────────────────────────────────────────────────
    public function updatingSearch()    { $this->resetPage(); }
    public function updatingStatus()    { $this->resetPage(); }
    public function updatingAuthorized(){ $this->resetPage(); }

    public function sortBy(string $col): void
    {
        if ($this->sortBy === $col) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $col;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    // ── Stats computed ────────────────────────────────────────────────────────
    public function getStatsProperty(): array
    {
        $base = EmailAccount::query();
        // Aislamiento estricto universal
        $base->where('user_id', auth()->id());
        return [
            'total'         => (clone $base)->count(),
            'active'        => (clone $base)->where('is_active', true)->count(),
            'inactive'      => (clone $base)->where('is_active', false)->count(),
            'authorized'    => (clone $base)->where('is_authorized', true)->count(),
            'unauthorized'  => (clone $base)->where('is_authorized', false)->count(),
        ];
    }

    // ── Servers paginated ─────────────────────────────────────────────────────
    public function getServersProperty()
    {
        $user  = auth()->user();

        $query = EmailAccount::with('user')
            ->when($this->search, fn($q, $s) => $q->where(fn($sq) =>
                $sq->where('email',     'like', "%{$s}%")
                   ->orWhere('username','like', "%{$s}%")
                   ->orWhere('imap_host','like',"%{$s}%")
            ))
            ->when($this->status !== '',     fn($q) => $q->where('is_active',    (bool)$this->status))
            ->when($this->authorized !== '', fn($q) => $q->where('is_authorized',(bool)$this->authorized))
            // Aislamiento estricto universal
            ->where('user_id', $user->id)
            ->orderBy($this->sortBy, $this->sortDir);

        return $query->paginate(12);
    }

    // ── Drawer detail ─────────────────────────────────────────────────────────
    public function openDrawer(int $id): void
    {
        $this->drawerServerId = $id;
        $this->testResult     = null;
        $this->testMessage    = '';
        $this->testData       = [];
    }

    public function closeDrawer(): void
    {
        $this->drawerServerId = null;
    }

    public function getDrawerServerProperty(): ?EmailAccount
    {
        return $this->drawerServerId
            ? EmailAccount::with('user')->where('user_id', auth()->id())->find($this->drawerServerId)
            : null;
    }

    // ── Quick IMAP Test from list ─────────────────────────────────────────────
    public function quickTest(int $id): void
    {
        $server = EmailAccount::findOrFail($id);
        if ($server->user_id !== auth()->id()) abort(403);
        $this->testingId  = $id;
        $this->testResult = null;

        try {
            $password = $server->imap_password;
            try { $password = Crypt::decryptString($password); } catch (\Exception) {}

            $portTest = ImapConnector::testConnection(
                $server->imap_host,
                $server->imap_port,
                $server->imap_encryption ?? 'ssl'
            );

            if (!$portTest['success']) {
                $this->testResult  = 'error';
                $this->testMessage = 'Puerto IMAP inaccesible: ' . $portTest['message'];
                $this->testingId   = null;
                return;
            }

            $enc   = $server->imap_encryption ?? 'ssl';
            $flags = match ($enc) {
                'ssl'  => '/imap/ssl/novalidate-cert',
                'tls'  => '/imap/tls/novalidate-cert',
                default => '/imap/ssl/novalidate-cert',
            };
            $mailbox    = "{{$server->imap_host}:{$server->imap_port}{$flags}}";
            @imap_errors();
            
            // [MODO DIOS] Forzar timeout corto para evitar que Nginx lance 504 Gateway Time-out
            imap_timeout(IMAP_OPENTIMEOUT, 10);
            imap_timeout(IMAP_READTIMEOUT, 10);
            imap_timeout(IMAP_WRITETIMEOUT, 10);
            
            $connection = @imap_open($mailbox, $server->username, $password, OP_READONLY, 1);

            if (!$connection) {
                $error = @imap_last_error();
                @imap_errors();
                $this->testResult  = 'error';
                $this->testMessage = $error ?: 'Credenciales inválidas.';
                $this->testingId   = null;

                // update last_checked
                $server->last_checked_at = now();
                $server->save();
                return;
            }

            $info  = @imap_mailboxmsginfo($connection);
            $count = $info->Nmsgs ?? 0;
            @imap_close($connection);

            $server->last_checked_at = now();
            $server->save();

            $this->testResult  = 'success';
            $this->testMessage = "¡Conexión exitosa! {$count} mensajes.";
            $this->testData    = ['host' => $server->imap_host, 'port' => $server->imap_port, 'messages' => $count];

        } catch (\Exception $e) {
            $this->testResult  = 'error';
            $this->testMessage = $e->getMessage();
        }

        $this->testingId = null;

        // If drawer is open for same server, refresh
        if ($this->drawerServerId === $id) {
            $this->drawerServerId = $id;
        }

        $this->dispatch('notif', message: $this->testResult === 'success'
            ? '✅ Conexión IMAP verificada'
            : '❌ Error de conexión IMAP');
    }

    // ── Toggle Active ─────────────────────────────────────────────────────────
    public function toggleActive(int $id): void
    {
        $server = EmailAccount::findOrFail($id);
        if ($server->user_id !== auth()->id()) abort(403);
        $server->is_active = !$server->is_active;
        $server->save();
        $this->dispatch('notif', message: $server->is_active ? '✅ Servidor activado' : '⏸ Servidor desactivado');
    }

    // ── Toggle Authorization ───────────────────────────────────────────────────
    public function toggleAuthorization(int $id): void
    {
        if (auth()->user()->id !== 1 && auth()->user()->role !== 'admin') abort(403);
        $server = EmailAccount::findOrFail($id);
        if ($server->user_id !== auth()->id()) abort(403);
        $server->is_authorized = !$server->is_authorized;
        $server->save();
        $this->dispatch('notif', message: $server->is_authorized ? '🔒 Servidor autorizado' : '🔓 Servidor desautorizado');
    }

    // ── Delete ─────────────────────────────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function deleteServer(): void
    {
        if (!$this->confirmDeleteId) return;
        $server = EmailAccount::findOrFail($this->confirmDeleteId);
        if ($server->user_id !== auth()->id()) abort(403);
        $server->delete();
        $this->confirmDeleteId = null;
        if ($this->drawerServerId) $this->drawerServerId = null;
        $this->dispatch('notif', message: '🗑 Servidor eliminado');
    }

    public function render()
    {
        return view('livewire.admin.server-list', [
            'emailAccounts' => $this->servers,
            'stats'         => $this->stats,
        ])->extends('admin.layouts.app')->section('content');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public static function detectProvider(string $host): array
    {
        $map = [
            'gmail'       => ['name' => 'Gmail',         'color' => '#EA4335', 'icon' => 'G'],
            'googlemail'  => ['name' => 'Gmail',         'color' => '#EA4335', 'icon' => 'G'],
            'outlook'     => ['name' => 'Outlook',       'color' => '#0078D4', 'icon' => 'O'],
            'office365'   => ['name' => 'Microsoft 365', 'color' => '#0078D4', 'icon' => 'M'],
            'yahoo'       => ['name' => 'Yahoo',         'color' => '#6001D2', 'icon' => 'Y'],
            'zoho'        => ['name' => 'Zoho',          'color' => '#E42527', 'icon' => 'Z'],
            'icloud'      => ['name' => 'iCloud',        'color' => '#007AFF', 'icon' => ''],
            'aol'         => ['name' => 'AOL',           'color' => '#FF0B00', 'icon' => 'A'],
            'yandex'      => ['name' => 'Yandex',        'color' => '#FC3F1D', 'icon' => 'Я'],
            'gmx'         => ['name' => 'GMX',           'color' => '#1D449B', 'icon' => 'G'],
            'titan'       => ['name' => 'Titan',         'color' => '#0F4C81', 'icon' => 'T'],
            'proton'      => ['name' => 'ProtonMail',    'color' => '#6D4AFF', 'icon' => 'P'],
            'mail'        => ['name' => 'Mail.com',      'color' => '#0052CC', 'icon' => 'M'],
        ];

        foreach ($map as $keyword => $info) {
            if (str_contains(strtolower($host), $keyword)) {
                return $info;
            }
        }

        return ['name' => 'Personalizado', 'color' => '#7c3aed', 'icon' => '⚙'];
    }
}
