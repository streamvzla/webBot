<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Client;
use App\Models\AllowedEmail;

class GlobalSearch extends Component
{

    public $search = '';
    public $results = [];
    public $isOpen = false;

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->results = [];
            return;
        }

        $term = '%' . $this->search . '%';
        $user = auth()->user();

        // Buscar Clientes
        $clientsQuery = Client::where(function($q) use ($term) {
            $q->where('name', 'like', $term)
              ->orWhere('email', 'like', $term);
        });
        
        if ($user && $user->role === 'user') {
            $clientsQuery->where('user_id', $user->id);
        }

        $clients = $clientsQuery->take(5)->get()->map(function($client) {
            return [
                'type' => 'Cliente',
                'title' => $client->name,
                'subtitle' => $client->email,
                'url' => route('admin.clients.edit', $client->id),
                'icon' => '<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>',
            ];
        });

        // Buscar Correos
        $emailsQuery = AllowedEmail::where('email', 'like', $term);
        
        if ($user && $user->role === 'user') {
            $emailsQuery->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('is_public', true);
            });
        }

        $emails = $emailsQuery->take(5)->get()->map(function($email) {
            return [
                'type' => 'Correo Autorizado',
                'title' => $email->email,
                'subtitle' => $email->client ? 'Asignado a: ' . $email->client->name : 'Libre',
                'url' => route('admin.allowed-emails.edit', $email->id),
                'icon' => '<svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
            ];
        });

        $this->results = collect($clients)->merge($emails)->take(8)->toArray();
    }

    public function closeSearch()
    {
        $this->isOpen = false;
        $this->search = '';
        $this->results = [];
    }
    public function render()
    {
        return view('livewire.admin.global-search');
    }
}
