<?php

namespace App\Livewire\Admin;

use App\Models\EmailAccount;
use App\Services\ImapConnector;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class ServerForm extends Component
{
    public ?EmailAccount $server = null;
    public $isEditMode = false;

    // Form fields
    public $email = '';
    public $imap_username = '';
    public $password = '';
    public $imap_host = 'imap.';
    public $imap_port = 993;
    public $imap_encryption = 'ssl';
    public $is_active = true;

    // Diagnostic State
    public $isDiagnosing = false;
    public $diagnosticResult = null; // null | 'success' | 'error'
    public $diagnosticMessage = '';
    public $diagnosticData = [];
    public $diagnosticSteps = []; // For terminal-like effect
    
    public $autoConfigMessage = '';

    public function mount(?EmailAccount $server = null)
    {
        if ($server && $server->exists) {
            if ($server->user_id !== auth()->id()) abort(403);
            $this->server = $server;
            $this->isEditMode = true;

            $this->email = $server->email;
            $this->imap_username = $server->username;
            // password is not prefilled for security, unless they type a new one
            $this->imap_host = $server->imap_host;
            $this->imap_port = $server->imap_port;
            $this->imap_encryption = $server->imap_encryption;
            $this->is_active = $server->is_active;
        }
    }

    public function updatedEmail($value)
    {
        $this->autoConfigMessage = ''; // Reset
        if (empty($value) || !str_contains($value, '@')) return;

        // Auto-fill imap_username if empty or matching old email
        if (empty($this->imap_username) || str_contains($this->imap_username, '@')) {
            $this->imap_username = $value;
        }

        $domain = strtolower(substr(strrchr($value, "@"), 1));

        $providers = [
            'gmail.com' => ['host' => 'imap.gmail.com', 'provider' => 'Gmail'],
            'googlemail.com' => ['host' => 'imap.gmail.com', 'provider' => 'Gmail'],
            
            'outlook.com' => ['host' => 'outlook.office365.com', 'provider' => 'Outlook'],
            'hotmail.com' => ['host' => 'outlook.office365.com', 'provider' => 'Hotmail'],
            'live.com' => ['host' => 'outlook.office365.com', 'provider' => 'Windows Live'],
            'msn.com' => ['host' => 'outlook.office365.com', 'provider' => 'MSN'],
            
            'yahoo.com' => ['host' => 'imap.mail.yahoo.com', 'provider' => 'Yahoo'],
            'ymail.com' => ['host' => 'imap.mail.yahoo.com', 'provider' => 'Yahoo'],
            
            'zoho.com' => ['host' => 'imap.zoho.com', 'provider' => 'Zoho'],
            'zohomail.com' => ['host' => 'imap.zoho.com', 'provider' => 'Zoho'],
            
            'icloud.com' => ['host' => 'imap.mail.me.com', 'provider' => 'iCloud'],
            'me.com' => ['host' => 'imap.mail.me.com', 'provider' => 'iCloud'],
            'mac.com' => ['host' => 'imap.mail.me.com', 'provider' => 'iCloud'],
            
            'aol.com' => ['host' => 'imap.aol.com', 'provider' => 'AOL'],
            'gmx.com' => ['host' => 'imap.gmx.com', 'provider' => 'GMX'],
            'gmx.net' => ['host' => 'imap.gmx.net', 'provider' => 'GMX'],
            'mail.com' => ['host' => 'imap.mail.com', 'provider' => 'Mail.com'],
            
            'yandex.com' => ['host' => 'imap.yandex.com', 'provider' => 'Yandex'],
            'yandex.ru' => ['host' => 'imap.yandex.com', 'provider' => 'Yandex'],
            
            'titan.email' => ['host' => 'imap.titan.email', 'provider' => 'Titan / Hostinger'],
        ];

        if (array_key_exists($domain, $providers)) {
            $this->imap_host = $providers[$domain]['host'];
            $this->imap_port = 993;
            $this->imap_encryption = 'ssl';
            $this->autoConfigMessage = '✨ Configuración automática aplicada para ' . $providers[$domain]['provider'];
        }
    }

    protected function rules()
    {
        return [
            'email' => 'required|email|max:255|unique:email_accounts,email' . ($this->isEditMode ? ',' . $this->server->id : ''),
            'imap_username' => 'required|string|max:255',
            'password' => $this->isEditMode ? 'nullable|string|max:255' : 'required|string|max:255',
            'imap_host' => 'required|string|max:255',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    public function testConnection()
    {
        // Validar solo lo necesario (sin unique para no bloquear la prueba)
        $this->validate([
            'email'          => 'required|email|max:255',
            'imap_username'  => 'required|string|max:255',
            'password'       => $this->isEditMode ? 'nullable|string|max:255' : 'required|string|max:255',
            'imap_host'      => 'required|string|max:255',
            'imap_port'      => 'required|integer',
        ]);

        $this->isDiagnosing   = true;
        $this->diagnosticResult = null;
        $this->diagnosticSteps  = [];

        $host       = trim($this->imap_host ?? '');
        $port       = (int) ($this->imap_port ?? 993);
        $encryption = $this->imap_encryption ?? 'ssl';
        $username   = trim($this->imap_username ?? '');

        // --- Resolver contraseña ---
        $plainPassword = trim($this->password ?? '');
        if ($this->isEditMode && empty($plainPassword)) {
            // Leer contraseña guardada directamente de la BD (fresca)
            $fresh = \App\Models\EmailAccount::withoutGlobalScopes()->find($this->server->id);
            if ($fresh) {
                $raw = $fresh->getRawOriginal('imap_password') 
                    ?? \DB::table('email_accounts')->where('id', $this->server->id)->value('imap_password');
                try {
                    $plainPassword = Crypt::decryptString($raw);
                } catch (\Exception $e) {
                    $plainPassword = (string) $raw;
                }
            }
        }

        $this->addDiagnosticStep("Conectando al host {$host}:{$port}...");

        // --- Paso 1: Prueba de socket (sin auth) ---
        $prefix = ($encryption === 'ssl') ? 'ssl://' : '';
        $socket = @fsockopen($prefix . $host, $port, $errno, $errstr, 8);
        if (!$socket) {
            $this->addDiagnosticStep("Puerto {$port} no accesible: {$errstr}", 'error');
            $this->setDiagnosticError('No se puede alcanzar el servidor IMAP.', "{$errstr} (código {$errno})");
            return;
        }
        fclose($socket);
        $this->addDiagnosticStep("Puerto {$port} alcanzable ✓");

        // --- Paso 3: Intento de autenticación directa con Webklex ---
        $this->addDiagnosticStep("Iniciando motor Webklex PHP-IMAP...");

        $originalTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 10); // [MODO DIOS PRO] Forzar timeout de socket a 10s

        try {
            $clientManager = new \Webklex\PHPIMAP\ClientManager();
            $client = $clientManager->make([
                'host'          => $host,
                'port'          => $port,
                'encryption'    => $encryption,
                'validate_cert' => false,
                'username'      => $username,
                'password'      => $plainPassword,
                'protocol'      => 'imap'
            ]);
            
            $client->connect();
            
            $this->addDiagnosticStep('Autenticación exitosa. Bandeja accedida correctamente.', 'success');

            $this->diagnosticResult  = 'success';
            $this->diagnosticMessage = '¡Conexión IMAP Exitosa!';
            $this->diagnosticData    = [
                'host'     => $host,
                'port'     => $port,
                'username' => $username,
                'messages' => 'OK',
            ];
        } catch (\Exception $e) {
            $this->addDiagnosticStep('Conexión fallida o credenciales rechazadas.', 'error');
            $this->setDiagnosticError('Error de autenticación IMAP.', $e->getMessage());
        }

        ini_set('default_socket_timeout', $originalTimeout); // Restaurar

        $this->isDiagnosing = false;
    }


    private function addDiagnosticStep($msg, $type = 'info')
    {
        $this->diagnosticSteps[] = [
            'msg' => $msg,
            'type' => $type
        ];
    }

    private function setDiagnosticError($title, $detail)
    {
        $this->diagnosticResult = 'error';
        $this->diagnosticMessage = $title;
        $this->diagnosticData = ['error' => $detail];
        $this->isDiagnosing = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'email' => $this->email,
            'username' => $this->imap_username,
            'imap_host' => $this->imap_host,
            'imap_port' => $this->imap_port,
            'imap_encryption' => $this->imap_encryption ?? 'ssl',
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['imap_password'] = Crypt::encryptString($this->password);
        }

        if ($this->isEditMode) {
            $this->server->update($data);
            session()->flash('success', 'Servidor actualizado exitosamente.');
        } else {
            $data['user_id'] = auth()->id();
            $data['is_authorized'] = true; // [AISLAMIENTO TOTAL] Se auto-autoriza porque cada quien maneja los suyos
            EmailAccount::create($data);
            session()->flash('success', 'Servidor creado y autorizado exitosamente.');
        }

        return redirect()->route('admin.servers.index');
    }

    public function render()
    {
        return view('livewire.admin.server-form')
            ->extends('admin.layouts.app')
            ->section('content');
    }
}
