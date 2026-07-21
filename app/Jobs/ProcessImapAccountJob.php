<?php

namespace App\Jobs;

use App\Models\EmailAccount;
use App\Models\ExtractedCode;
use App\Models\Platform;
use App\Models\AllowedEmail;
use App\Services\ImapConnector;
use App\Services\EmailCodeExtractor;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessImapAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $account;
    const CODE_TTL_MINUTES = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailAccount $account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // [LIMITADOR EXTREMO] Evitar que un worker se quede colgado eternamente si Gmail bloquea el socket TCP
        ini_set('default_socket_timeout', 15);

        $echoPrefix = "[" . $this->account->email . "]";
        echo "{$echoPrefix} Iniciando revisión de cuenta...\n";

        // Cargar plataformas y mapeo de asuntos una sola vez
        $platforms = Platform::where('is_active', true)->with('subjects')->get();
        $platformSubjects = $this->buildPlatformSubjectsMap($platforms);

        // Cargar correos válidos UNA SOLA VEZ (Elimina N+1 Queries)
        $expectedRecipients = AllowedEmail::pluck('email')
            ->map(function ($email) {
                return strtolower(trim($email));
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Variable estática para mantener la conexión viva entre Jobs (ya que el worker es un daemon)
        static $activeConnectors = [];
        $connector = null;

        try {
            if (!isset($activeConnectors[$this->account->id])) {
                $connector = new ImapConnector($this->account);
                $connector->connect();
                $activeConnectors[$this->account->id] = $connector;
            } else {
                $connector = $activeConnectors[$this->account->id];
                if (!$connector->isConnected()) {
                    $connector->connect();
                }
            }

            echo "{$echoPrefix} Conectado OK\n";

            $messages = $connector->getRecentEmails();

            if (empty($messages)) {
                echo "{$echoPrefix} Correos sin leer: 0\n";
                $this->account->update(['error_count' => 0]);
                return;
            }

            echo "{$echoPrefix} Correos sin leer: " . count($messages) . "\n";
            $messagesToProcess = array_slice($messages, 0, 20);
            echo "{$echoPrefix} Procesando los " . count($messagesToProcess) . " más recientes...\n";

            // === CARGA EN BLOQUE A BD (Optimización N+1) ===
            $uids = array_map(function ($message) {
                return (string) $message->getUid();
            }, $messagesToProcess);

            $alreadyProcessed = ExtractedCode::where('email_account_id', $this->account->id)
                ->whereIn('uid', $uids)
                ->pluck('uid')
                ->toArray();
            // ===============================================

            foreach ($messagesToProcess as $message) {
                $uid = (string) $message->getUid();

                // NINJA REAL: Si el correo ya fue marcado como LEIDO (Seen) en el servidor de Google,
                if ($message->hasFlag('Seen') || $message->hasFlag('SEEN') || $message->hasFlag('\\Seen')) {
                    continue;
                }

                if (in_array($uid, $alreadyProcessed)) {
                    continue;
                }

                $this->processEmail($connector, $this->account, $message, $platforms, $platformSubjects, $expectedRecipients, $echoPrefix);
            }

            $this->account->update(['error_count' => 0]);

        } catch (\Throwable $e) {
            $this->account->increment('error_count');
            echo "{$echoPrefix} Error: " . $e->getMessage() . "\n";
            Log::warning('[Centinela Worker] Error procesando cuenta', [
                'account' => $this->account->email,
                'error'   => $e->getMessage(),
            ]);
            // Si hay un error, forzamos la desconexión y destruimos el conector cacheado
            try { $connector?->disconnect(); } catch (\Throwable $ex) {}
            unset($activeConnectors[$this->account->id]);
        } finally {
            // NOTA: Ya no nos desconectamos en el 'finally' si fue exitoso, 
            // así reutilizamos la sesión IMAP en el próximo Job.
            
            // Liberar el Semáforo para que el Director pueda enviar una nueva revisión
            Cache::forget('imap_account_' . $this->account->id);
        }
    }

    private function processEmail(
        ImapConnector $connector,
        EmailAccount  $account,
        $message,
        $platforms,
        array         $platformSubjects,
        array         $expectedRecipients,
        string        $echoPrefix
    ): void {
        $uid = $message->getUid();

        // Buscar email, validar destinatarios y asunto con Webklex usando lazy loading
        $emailData = $connector->searchByTo($message, $expectedRecipients, $platformSubjects);

        if (!$emailData) {
            echo "  {$echoPrefix} -> UID {$uid} | Omitido (No coincide destinatario/asunto)\n";
            $connector->markAsRead($uid);
            return;
        }

        $toEmail = $emailData['to'];
        $subject = $emailData['subject'];
        $body    = $emailData['body'];
        
        $matchedPlatformName = $emailData['platform_name'] ?? null;
        $matchedPlatform = $platforms->firstWhere('name', $matchedPlatformName);

        if (!$matchedPlatform) {
            echo "  {$echoPrefix} -> UID {$uid} | Error: Plataforma no encontrada en BD ($matchedPlatformName)\n";
            $connector->markAsRead($uid);
            return;
        }

        echo "  {$echoPrefix} -> UID {$uid} | Para: {$toEmail} | Asunto: " . mb_substr($subject, 0, 50) . "\n";
        echo "  {$echoPrefix} -> Plataforma: {$matchedPlatform->name}\n";

        $cleanText = strip_tags($body);
        $extractedData  = EmailCodeExtractor::extract($body, $cleanText);
        $extractedValue = is_array($extractedData) ? ($extractedData['value'] ?? null) : $extractedData;
        $extractedType  = is_array($extractedData) ? ($extractedData['type']  ?? 'code') : 'code';

        if (!$extractedValue) {
            echo "  {$echoPrefix} -> No se pudo extraer codigo del UID {$uid}\n";
            Log::info('[Centinela Worker] Sin codigo extraible', [
                'uid' => $uid,
                'subject' => $subject,
                'platform' => $matchedPlatform->name
            ]);
            $connector->markAsRead($uid);
            return;
        }

        $codigoCorto = mb_substr(strip_tags($extractedValue), 0, 100);
        echo "  {$echoPrefix} -> Tipo: {$extractedType} | Valor: {$codigoCorto}\n";

        ExtractedCode::create([
            'email_account_id' => $account->id,
            'platform_id'      => $matchedPlatform->id,
            'recipient_email'  => $toEmail,
            'code'             => (string) $extractedValue,
            'code_type'        => $extractedType,
            'body'             => mb_substr($cleanText, 0, 50000),
            'uid'              => (string) $uid,
            'expires_at'       => now()->addMinutes(self::CODE_TTL_MINUTES),
            'subject'          => mb_substr((string) $subject, 0, 255),
        ]);

        $connector->markAsRead($uid);

        echo "  {$echoPrefix} -> Codigo guardado OK: {$codigoCorto} para {$toEmail} ({$matchedPlatform->name})\n";

        Log::info('[Centinela Worker] Codigo capturado', [
            'platform'  => $matchedPlatform->name,
            'recipient' => $toEmail,
            'code'      => $codigoCorto,
            'uid'       => $uid,
            'account'   => $account->email,
        ]);
    }

    private function buildPlatformSubjectsMap($platforms): array
    {
        $map = [];
        foreach ($platforms as $platform) {
            $subjects = $platform->subjects()
                                  ->where('is_active', true)
                                  ->pluck('subject')
                                  ->toArray();
            $map[$platform->name] = array_map(fn($s) => $this->normalize($s), $subjects);
        }
        return $map;
    }

    private function normalize(string $text): string
    {
        $ascii = \Illuminate\Support\Str::ascii($text);
        return preg_replace('/[^a-z0-9]/', '', strtolower($ascii));
    }
}
