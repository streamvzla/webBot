<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use Carbon\Carbon;

class ClientList extends Component
{

    use WithPagination;

    public $search = '';
    public $status = '';
    
    // Acciones Masivas
    public $selectedIds = [];
    public $selectAll = false;

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->buildQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    private function buildQuery()
    {
        $user = auth()->user();

        $query = Client::with('emailAccount')
            ->with('user')
            ->with('platforms')
            ->withCount('allowedEmails');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->status !== '') {
            $query->where('is_active', (bool)$this->status);
        }

        // Aislamiento estricto (Aplica para todos los roles)
        if ($user) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function deleteSelected()
    {
        if(count($this->selectedIds)) {
            Client::whereIn('id', $this->selectedIds)->delete();
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function activateSelected()
    {
        if(count($this->selectedIds)) {
            Client::whereIn('id', $this->selectedIds)->update(['is_active' => true]);
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function deactivateSelected()
    {
        if(count($this->selectedIds)) {
            Client::whereIn('id', $this->selectedIds)->update(['is_active' => false]);
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function renewSelected()
    {
        if(count($this->selectedIds)) {
            // Añadir +30 días a todos los correos asignados a los clientes seleccionados
            $clients = Client::whereIn('id', $this->selectedIds)->with('allowedEmails')->get();
            foreach($clients as $client) {
                foreach($client->allowedEmails as $email) {
                    $expires = $email->pivot->expires_at ? Carbon::parse($email->pivot->expires_at) : now();
                    if($expires->isPast()) $expires = now();
                    
                    $client->allowedEmails()->updateExistingPivot($email->id, [
                        'expires_at' => $expires->addDays(30)
                    ]);
                }
            }
            
            $this->selectedIds = [];
            $this->selectAll = false;
            session()->flash('success', 'Renovación masiva aplicada (+30 días)');
        }
    }

    public function with()
    {
        return [
            'clients' => $this->buildQuery()->orderBy('name')->paginate(20),
            'showParentColumn' => !(auth()->user() && auth()->user()->role === 'user')
        ];
    }
    public function render()
    {
        return view('livewire.admin.client-list');
    }
}
