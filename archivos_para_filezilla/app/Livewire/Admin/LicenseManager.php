<?php

namespace App\Livewire\Admin;

use App\Models\License;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class LicenseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Create / Edit are now handled by LicenseForm component.

    public function mount()
    {
        if (auth()->id() !== 1) {
            abort(403);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteLicense($id)
    {
        $license = License::find($id);
        if ($license) {
            $license->delete();
            $this->dispatch('toast', [['type' => 'success', 'message' => 'Licencia eliminada permanentemente']]);
        }
    }

    public function render()
    {
        $query = License::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('license_key', 'like', '%' . $this->search . '%')
                  ->orWhere('domain', 'like', '%' . $this->search . '%')
                  ->orWhere('client_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('components.admin.license-manager', [
            'licenses' => $query->latest()->paginate(15),
            'metrics' => [
                'total' => License::count(),
                'active' => License::where('status', 'active')->count(),
                'suspended' => License::where('status', 'suspended')->count(),
                'revoked' => License::where('status', 'revoked')->count(),
            ]
        ])->extends('admin.layouts.app')->section('content');
    }
}
