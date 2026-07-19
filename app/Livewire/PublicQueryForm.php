<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Platform;
use App\Models\ExtractedCode;
use App\Models\Query;
use App\Models\AllowedEmail;

class PublicQueryForm extends Component
{
    public $email = '';
    
    // Result State
    public $resultStatus = null; // null, 'success', 'not_found', 'error', 'not_authorized'
    public $resultData = null; 
    
    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => '¡Ups! Necesitamos tu correo para poder buscar tu código.',
        'email.email' => 'El correo parece ser incorrecto. Revisa que esté bien escrito.',
    ];

    public function resetForm()
    {
        $this->reset(['email', 'resultStatus', 'resultData']);
    }

    public function submit()
    {
        $this->validate();

        try {
            // VERIFICACIÓN DE SEGURIDAD
            $allowedEmail = AllowedEmail::where('email', $this->email)
                ->where('is_active', true)
                ->where('is_public', true)
                ->first();

            if (!$allowedEmail) {
                $this->resultStatus = 'not_authorized';
                return;
            }

            // BUSQUEDA INSTANTANEA GLOBAL EN LA BASE DE DATOS DEL CENTINELA
            $extractedCodeModel = ExtractedCode::with('platform')
                ->where('recipient_email', $this->email)
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->first();

            $foundCode = $extractedCodeModel !== null;
            
            // Registrar consulta pública en la tabla queries
            Query::create([
                'email_account_id' => null,
                'platform_id' => $foundCode ? $extractedCodeModel->platform_id : null,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'result' => $foundCode ? 'success' : 'no_code',
                'code_hash' => $foundCode ? Query::hashCode($extractedCodeModel->code ?? '') : null,
                'code_status' => $foundCode ? 'found' : 'not_found',
            ]);

            if ($foundCode) {
                $displaySeconds = config('app.code_display_seconds', 60);
                
                $this->resultData = [
                    'body' => $extractedCodeModel->body,
                    'code' => $extractedCodeModel->code,
                    'received_at' => $extractedCodeModel->created_at->format('Y-m-d H:i:s'),
                    'expires_in' => $displaySeconds,
                    'platform_name' => $extractedCodeModel->platform ? $extractedCodeModel->platform->name : 'Plataforma Desconocida',
                    'platform_logo' => $extractedCodeModel->platform ? $extractedCodeModel->platform->logo : null,
                ];
                
                $this->resultStatus = 'success';
            } else {
                $this->resultStatus = 'not_found';
            }
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error en Livewire PublicQueryForm: " . $e->getMessage());
            $this->resultStatus = 'error';
        }
    }

    public function render()
    {
        return view('livewire.public-query-form');
    }
}
