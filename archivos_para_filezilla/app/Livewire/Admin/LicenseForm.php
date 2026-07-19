<?php

namespace App\Livewire\Admin;

use App\Models\License;
use Illuminate\Support\Str;
use Livewire\Component;

class LicenseForm extends Component
{
    public $licenseId = null;
    public $isEditing = false;

    // Form fields
    public $license_key = '';
    public $domain = '';
    public $client_name = '';
    public $client_email = '';
    public $status = 'active';
    public $notes = '';
    public $max_clients = null;
    public $max_queries_day = null;

    public function mount(License $license = null)
    {
        if (auth()->id() !== 1) {
            abort(403);
        }

        if ($license && $license->exists) {
            $this->isEditing = true;
            $this->licenseId = $license->id;
            
            $this->license_key     = $license->license_key;
            $this->domain          = $license->domain;
            $this->client_name     = $license->client_name;
            $this->client_email    = $license->client_email;
            $this->status          = $license->status;
            $this->notes           = $license->notes;
            $this->max_clients     = $license->max_clients;
            $this->max_queries_day = $license->max_queries_day;
        } else {
            $this->generateKey();
        }
    }

    public function generateKey()
    {
        $this->license_key = 'TCD-' . strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
    }

    public function save()
    {
        $rules = [
            'license_key' => 'required|string|max:100|unique:licenses,license_key' . ($this->isEditing ? ',' . $this->licenseId : ''),
            'domain' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'status' => 'required|in:active,suspended,revoked',
        ];

        $this->validate($rules);

        if ($this->isEditing) {
            $license = License::findOrFail($this->licenseId);
            $license->update([
                'license_key'     => $this->license_key,
                'domain'          => $this->domain,
                'client_name'     => $this->client_name,
                'client_email'    => $this->client_email,
                'status'          => $this->status,
                'notes'           => $this->notes,
                'max_clients'     => $this->max_clients ?: null,
                'max_queries_day' => $this->max_queries_day ?: null,
            ]);
            
            session()->flash('success', 'Licencia actualizada exitosamente');
        } else {
            License::create([
                'license_key'     => $this->license_key,
                'domain'          => $this->domain,
                'client_name'     => $this->client_name,
                'client_email'    => $this->client_email,
                'status'          => $this->status,
                'notes'           => $this->notes,
                'max_clients'     => $this->max_clients ?: null,
                'max_queries_day' => $this->max_queries_day ?: null,
            ]);
            
            session()->flash('success', 'Licencia generada exitosamente');
        }

        return $this->redirect(route('admin.licenses.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.license-form')
            ->extends('admin.layouts.app')
            ->section('content');
    }
}
