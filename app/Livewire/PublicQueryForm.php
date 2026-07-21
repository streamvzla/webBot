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
            $allowedEmail = AllowedEmail::with(['emailAccount', 'platform'])
                ->where('email', $this->email)
                ->where('is_active', true)
                ->where('is_public', true)
                ->first();

            if (!$allowedEmail) {
                $this->resultStatus = 'not_authorized';
                return;
            }

            if (!$allowedEmail->emailAccount || !$allowedEmail->platform) {
                $this->resultStatus = 'error';
                \Illuminate\Support\Facades\Log::error("Falta cuenta IMAP o plataforma para " . $this->email);
                return;
            }

            // CACHÉ DE 10 SEGUNDOS (Protección de concurrencia)
            // Si 5 clientes piden a la vez, solo el primero conecta a IMAP.
            $cacheKey = 'live_imap_query_' . md5($this->email);
            
            $foundCode = false;
            $codeData = null;

            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $cachedResult = \Illuminate\Support\Facades\Cache::get($cacheKey);
                if ($cachedResult !== 'not_found') {
                    $foundCode = true;
                    $codeData = $cachedResult;
                }
            } else {
                // EXTRACCIÓN BAJO DEMANDA (EN VIVO)
                $connector = new \App\Services\ImapConnector($allowedEmail->emailAccount);
                $connector->connect();

                // Traer los últimos correos (optimizado a 5 por el conector)
                $messages = $connector->getRecentEmails(1);
                
                $matchedPlatform = $allowedEmail->platform;
                $expectedRecipients = [strtolower(trim($this->email))];
                $platformSubjects = [$matchedPlatform->name => $matchedPlatform->subject_keywords];

                foreach ($messages as $message) {
                    $emailData = $connector->searchByTo($message, $expectedRecipients, $platformSubjects);
                    
                    if ($emailData) {
                        $cleanText = strip_tags($emailData['body']);
                        $extracted = \App\Services\EmailCodeExtractor::extract($emailData['body'], $cleanText);
                        
                        $val = is_array($extracted) ? ($extracted['value'] ?? null) : $extracted;
                        
                        if ($val) {
                            $foundCode = true;
                            // Asumimos que si lo encontró, llegó "ahora" (o usamos la fecha real del correo si fuera necesario)
                            $receivedAt = now()->format('Y-m-d H:i:s'); 
                            
                            $codeData = [
                                'body' => $emailData['body'],
                                'code' => $val,
                                'received_at' => $receivedAt,
                                'platform_name' => $matchedPlatform->name,
                                'platform_logo' => $matchedPlatform->logo,
                            ];
                            break; // Detenerse en el primero (el más reciente) que coincida
                        }
                    }
                }
                
                // Guardar en caché por 10 segundos
                \Illuminate\Support\Facades\Cache::put($cacheKey, $foundCode ? $codeData : 'not_found', 10);
                
                try {
                    $connector->disconnect();
                } catch (\Throwable $e) {}
            }

            // Registrar consulta pública en la tabla queries
            Query::create([
                'email_account_id' => $allowedEmail->email_account_id,
                'platform_id' => $allowedEmail->platform_id,
                'email' => $this->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'result' => $foundCode ? 'success' : 'no_code',
                'code_hash' => $foundCode ? Query::hashCode($codeData['code'] ?? '') : null,
                'code_status' => $foundCode ? 'found' : 'not_found',
            ]);

            if ($foundCode) {
                $displaySeconds = config('app.code_display_seconds', 60);
                $codeData['expires_in'] = $displaySeconds;
                
                $this->resultData = $codeData;
                $this->resultStatus = 'success';
            } else {
                $this->resultStatus = 'not_found';
            }
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error en Livewire PublicQueryForm (On-Demand): " . $e->getMessage());
            $this->resultStatus = 'error';
        }
    }

    public function render()
    {
        return view('livewire.public-query-form');
    }
}
