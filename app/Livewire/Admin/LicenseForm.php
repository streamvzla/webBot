<?php

namespace App\Livewire\Admin;

use App\Models\License;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
    public $days = 30;           // Días de membresía
    public $create_user = true;  // Crear usuario admin automáticamente

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
            'license_key'  => 'required|string|max:100|unique:licenses,license_key' . ($this->isEditing ? ',' . $this->licenseId : ''),
            'domain'       => 'nullable|string|max:255',
            'client_name'  => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'status'       => 'required|in:active,suspended,revoked',
            'days'         => 'nullable|integer|min:1|max:3650',
        ];

        $this->validate($rules);

        $expiresAt = $this->days ? now()->addDays((int)$this->days) : null;

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
                'notes'           => $this->notes . ($expiresAt ? " | Vence: {$expiresAt->toDateString()}" : ''),
                'max_clients'     => $this->max_clients ?: null,
                'max_queries_day' => $this->max_queries_day ?: null,
            ]);

            // Crear usuario admin automáticamente si se activó la opción
            if ($this->create_user && $this->client_name) {
                $domain       = Setting::get(Setting::KEY_AUTO_USER_DOMAIN, 'tu-codigo.com');
                $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $this->client_name)[0]));
                if (empty($baseUsername)) $baseUsername = 'user';

                $username = $baseUsername;
                $counter  = 2;
                while (User::where('name', $username)->orWhere('email', "{$username}@{$domain}")->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $plainPassword = strtoupper(Str::random(4)) . random_int(10, 99) . '@' . Str::random(4);

                User::create([
                    'name'                 => $username,
                    'email'                => "{$username}@{$domain}",
                    'password'             => Hash::make($plainPassword),
                    'role'                 => 'admin',
                    'email_verified_at'    => now(),
                    'subscription_ends_at' => $expiresAt,
                ]);

                session()->flash('success',
                    "Licencia creada | Usuario: {$username}@{$domain} | Contraseña: {$plainPassword}"
                    . ($expiresAt ? " | Vence: {$expiresAt->toDateString()}" : '')
                );
            } else {
                session()->flash('success', 'Licencia generada exitosamente'
                    . ($expiresAt ? " (Vence: {$expiresAt->toDateString()})" : ''));
            }
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
