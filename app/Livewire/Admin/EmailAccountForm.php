<?php

namespace App\Livewire\Admin;

use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class EmailAccountForm extends Component
{
    public ?EmailAccount $account = null;
    public bool $isEditMode = false;

    // ── Form fields ────────────────────────────────────────────────────────────
    public string  $email          = '';
    public string  $imap_username  = '';
    public string  $password       = '';
    public string  $imap_host      = '';
    public int     $imap_port      = 993;
    public string  $imap_encryption = 'ssl';
    public bool    $is_active      = true;

    // ── User assignment ────────────────────────────────────────────────────────
    public array  $selectedUserIds = [];
    public string $userSearch      = '';

    // ── Auto-config ────────────────────────────────────────────────────────────
    public string $autoConfigMessage = '';

    public function mount(?EmailAccount $account = null): void
    {
        if ($account && $account->exists) {
            $this->account      = $account;
            $this->isEditMode   = true;
            $this->email        = $account->email;
            $this->imap_username = $account->username;
            $this->imap_host    = $account->imap_host;
            $this->imap_port    = $account->imap_port;
            $this->imap_encryption = $account->imap_encryption ?? 'ssl';
            $this->is_active    = $account->is_active;
            $this->selectedUserIds = $account->users->pluck('id')->toArray();
        }
    }

    // ── Auto-config by email provider ─────────────────────────────────────────
    public function updatedEmail(string $value): void
    {
        $this->autoConfigMessage = '';
        if (empty($value) || !str_contains($value, '@')) return;

        if (empty($this->imap_username)) {
            $this->imap_username = $value;
        }

        $domain = strtolower(substr(strrchr($value, '@'), 1));
        $providers = [
            'gmail.com'     => ['host' => 'imap.gmail.com',           'name' => 'Gmail'],
            'googlemail.com'=> ['host' => 'imap.gmail.com',           'name' => 'Gmail'],
            'outlook.com'   => ['host' => 'outlook.office365.com',    'name' => 'Outlook'],
            'hotmail.com'   => ['host' => 'outlook.office365.com',    'name' => 'Hotmail'],
            'live.com'      => ['host' => 'outlook.office365.com',    'name' => 'Windows Live'],
            'msn.com'       => ['host' => 'outlook.office365.com',    'name' => 'MSN'],
            'yahoo.com'     => ['host' => 'imap.mail.yahoo.com',      'name' => 'Yahoo'],
            'ymail.com'     => ['host' => 'imap.mail.yahoo.com',      'name' => 'Yahoo'],
            'icloud.com'    => ['host' => 'imap.mail.me.com',         'name' => 'iCloud'],
            'me.com'        => ['host' => 'imap.mail.me.com',         'name' => 'iCloud'],
            'mac.com'       => ['host' => 'imap.mail.me.com',         'name' => 'iCloud'],
            'zoho.com'      => ['host' => 'imap.zoho.com',            'name' => 'Zoho'],
            'zohomail.com'  => ['host' => 'imap.zoho.com',            'name' => 'Zoho'],
            'aol.com'       => ['host' => 'imap.aol.com',             'name' => 'AOL'],
            'gmx.com'       => ['host' => 'imap.gmx.com',             'name' => 'GMX'],
            'gmx.net'       => ['host' => 'imap.gmx.net',             'name' => 'GMX'],
            'mail.com'      => ['host' => 'imap.mail.com',            'name' => 'Mail.com'],
            'yandex.com'    => ['host' => 'imap.yandex.com',          'name' => 'Yandex'],
            'yandex.ru'     => ['host' => 'imap.yandex.com',          'name' => 'Yandex'],
            'titan.email'   => ['host' => 'imap.titan.email',         'name' => 'Titan / Hostinger'],
            'protonmail.com'=> ['host' => 'imap.protonmail.com',      'name' => 'ProtonMail'],
        ];

        if (isset($providers[$domain])) {
            $this->imap_host        = $providers[$domain]['host'];
            $this->imap_port        = 993;
            $this->imap_encryption  = 'ssl';
            $this->autoConfigMessage = '✨ Configuración automática: ' . $providers[$domain]['name'];
        }
    }

    // ── User search computed ───────────────────────────────────────────────────
    public function getUsersProperty()
    {
        return User::where('is_active', true)
            ->when($this->userSearch, fn($q) => $q->where(fn($sq) =>
                $sq->where('name',     'like', "%{$this->userSearch}%")
                   ->orWhere('username','like', "%{$this->userSearch}%")
                   ->orWhere('email',  'like', "%{$this->userSearch}%")
            ))
            ->orderBy('name')
            ->get();
    }

    public function toggleUser(int $userId): void
    {
        if (in_array($userId, $this->selectedUserIds)) {
            $this->selectedUserIds = array_values(array_filter($this->selectedUserIds, fn($id) => $id !== $userId));
        } else {
            $this->selectedUserIds[] = $userId;
        }
    }

    public function removeUser(int $userId): void
    {
        $this->selectedUserIds = array_values(array_filter($this->selectedUserIds, fn($id) => $id !== $userId));
    }

    // ── Validation ─────────────────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'email'          => 'required|email|max:255',
            'imap_username'  => 'required|string|max:255',
            'password'       => $this->isEditMode ? 'nullable|string|max:255' : 'required|string|max:255',
            'imap_host'      => 'required|string|max:255',
            'imap_port'      => 'required|integer|min:1|max:65535',
            'imap_encryption'=> 'nullable|string|max:50',
            'is_active'      => 'boolean',
            'selectedUserIds'=> 'array',
            'selectedUserIds.*' => 'exists:users,id',
        ];
    }

    // ── Save ───────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $this->validate();

        $data = [
            'email'          => $this->email,
            'username'       => $this->imap_username,
            'imap_host'      => $this->imap_host,
            'imap_port'      => $this->imap_port,
            'imap_encryption'=> $this->imap_encryption ?? 'ssl',
            'is_active'      => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['imap_password'] = Crypt::encryptString($this->password);
        }

        if ($this->isEditMode) {
            $this->account->update($data);
            $this->account->users()->sync($this->selectedUserIds);
            session()->flash('success', 'Cuenta actualizada exitosamente.');
        } else {
            $acc = EmailAccount::create($data);
            if (!empty($this->selectedUserIds)) {
                $acc->users()->attach($this->selectedUserIds);
            }
            session()->flash('success', 'Cuenta creada exitosamente.');
        }

        $this->redirect(route('admin.email-accounts.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.email-account-form', [
            'allUsers' => $this->users,
        ])->extends('admin.layouts.app')->section('content');
    }
}
