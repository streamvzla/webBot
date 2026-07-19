<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AllowedEmail;
use App\Models\Platform;
use App\Models\User;

class AllowedEmailList extends Component
{

    use WithPagination;

    public $search = '';
    public $assignment = '';
    public $status = '';
    public $public = '';
    public $platform_id = '';
    public $user_id = '';
    
    // Acciones Masivas
    public $selectedIds = [];
    public $selectAll = false;

    // Vista
    public $view = 'cards';

    public function getStatsProperty()
    {
        $base = AllowedEmail::visibleToUser(auth()->user());
        
        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('is_active', true)->count(),
            'inactive' => (clone $base)->where('is_active', false)->count(),
            'public' => (clone $base)->where('is_public', true)->count(),
        ];
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedAssignment() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }
    public function updatedPublic() { $this->resetPage(); }
    public function updatedPlatformId() { $this->resetPage(); }
    public function updatedUserId() { $this->resetPage(); }

    public $assignToUserId = '';

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->buildQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function assignSelectedToUser()
    {
        if (empty($this->selectedIds) || empty($this->assignToUserId)) {
            $this->dispatch('notif', message: '⚠️ Selecciona correos y un miembro de equipo.');
            return;
        }

        $userId = $this->assignToUserId === 'unassign' ? null : $this->assignToUserId;

        AllowedEmail::whereIn('id', $this->selectedIds)->update(['assigned_to' => $userId]);

        $this->selectedIds = [];
        $this->selectAll = false;
        $this->assignToUserId = '';
        
        $this->dispatch('notif', message: "✅ Inventario asignado correctamente.");
    }

    private function buildQuery()
    {
        $user = auth()->user();

        $query = AllowedEmail::visibleToUser($user)
            ->with(['platform', 'user', 'assignedTo'])
            ->withCount([
                'clients as total_clients_count',
                'clients as active_clients_count' => function ($q) {
                    $q->where(function ($c) {
                        $c->whereNull('allowed_email_client.expires_at')
                          ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                    });
                },
                'clients as expired_clients_count' => function ($q) {
                    $q->whereNotNull('allowed_email_client.expires_at')
                      ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                },
            ]);

        if (!empty($this->search)) {
            $query->where('email', 'like', "%{$this->search}%");
        }
        if ($this->status !== '') {
            $query->where('is_active', (bool)$this->status);
        }
        if ($this->public !== '') {
            $query->where('is_public', (bool)$this->public);
        }
        if (!empty($this->platform_id)) {
            $query->where('platform_id', $this->platform_id);
        }
        if (!empty($this->user_id)) {
            $query->where('user_id', $this->user_id);
        }
        
        if ($this->assignment === 'free') {
            $query->where(function ($q) {
                $q->doesntHave('clients')
                  ->orWhereDoesntHave('clients', function ($inner) {
                      $inner->where(function ($c) {
                          $c->whereNull('allowed_email_client.expires_at')
                            ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                      });
                  });
            });
        } elseif ($this->assignment === 'assigned') {
            $query->whereHas('clients', function ($inner) {
                $inner->where(function ($c) {
                    $c->whereNull('allowed_email_client.expires_at')
                      ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                });
            });
        } elseif ($this->assignment === 'expired') {
            $query->whereHas('clients', function ($inner) {
                $inner->whereNotNull('allowed_email_client.expires_at')
                      ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
            });
        }

        if ($user && $user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function deleteSelected()
    {
        if(count($this->selectedIds)) {
            AllowedEmail::whereIn('id', $this->selectedIds)->delete();
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function activateSelected()
    {
        if(count($this->selectedIds)) {
            AllowedEmail::whereIn('id', $this->selectedIds)->update(['is_active' => true]);
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function deactivateSelected()
    {
        if(count($this->selectedIds)) {
            AllowedEmail::whereIn('id', $this->selectedIds)->update(['is_active' => false]);
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function makePublicSelected()
    {
        if(count($this->selectedIds)) {
            AllowedEmail::whereIn('id', $this->selectedIds)->update(['is_public' => true]);
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function makePrivateSelected()
    {
        if(count($this->selectedIds)) {
            AllowedEmail::whereIn('id', $this->selectedIds)->update(['is_public' => false]);
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function with()
    {
        return [
            'allowedEmails' => $this->buildQuery()->orderBy('email')->paginate(20),
            'platforms' => Platform::where('user_id', auth()->id())->orderBy('name')->get(),
            'users' => User::whereIn('id', auth()->user()->getDescendantsIds())->orderBy('name')->get(),
            'teamMembers' => User::where('parent_id', auth()->id())->orderBy('username')->get(),
        ];
    }
    public function render()
    {
        return view('livewire.admin.allowed-email-list');
    }
}
