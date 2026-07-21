<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailAccount;
use App\Models\Platform;
use App\Models\ExtractedCode;
use App\Services\ImapConnector;
use App\Services\EmailCodeExtractor;
use Illuminate\Support\Facades\Log;

class ImapSentinelCommand extends Command
{
    protected $signature   = 'imap:sentinel';
    protected $description = 'Robot Centinela: Revisa correos en background y guarda codigos en BD';

    private const LOOKBACK_HOURS   = 2;
    private const CODE_TTL_MINUTES = 15;
    private const IMAP_TIMEOUT     = 5; // Limite maximo absoluto por servidor
    private const SLEEP_BASE       = 5;
    private const SLEEP_JITTER     = 3;
    private const MAX_ERRORS       = 3;

    private array $errorCount = [];

    public function handle(): void
    {
        $this->info('Iniciando Robot Centinela IMAP...');
        Log::info('[Centinela] Proceso iniciado');

        // [LIMITADOR EXTREMO] Forzar que PHP aborte cualquier conexion muerta a los 5 segundos
        // para garantizar que pase al siguiente servidor instantaneamente
        ini_set('default_socket_timeout', 5);

        while (true) {
            try {
                $this->runCycle();
            } catch (\Throwable $e) {
                $this->error('[Centinela] Error global: ' . $e->getMessage());
                Log::error('[Centinela] Error global', ['error' => $e->getMessage()]);
            }
            $sleep = self::SLEEP_BASE + rand(-self::SLEEP_JITTER, self::SLEEP_JITTER);
            sleep(max(2, $sleep));
        }
    }

    private function runCycle(): void
    {
        $deleted = ExtractedCode::where('expires_at', '<', now())->delete();
        if ($deleted > 0) {
            $this->line("Limpiados {$deleted} codigo(s) vencido(s)");
        }

        $accounts  = EmailAccount::where('is_active', true)
                                  ->where('is_authorized', true)
                                  ->get();

        $platforms = Platform::where('is_active', true)->get();

        if ($accounts->isEmpty()) {
            $this->warn('No hay cuentas activas. Esperando...');
            sleep(10);
            return;
        }

        if ($platforms->isEmpty()) {
            $this->warn('No hay plataformas activas configuradas.');
            return;
        }

        $platformSubjects = $this->buildPlatformSubjectsMap($platforms);

        foreach ($accounts as $account) {
            $this->processAccount($account, $platforms, $platformSubjects);
        }
    }

    private function processAccount(EmailAccount $account, $platforms, array $platformSubjects): void
    {
        $this->info('Revisando cuenta: ' . $account->email);

        $errors = $this->errorCount[$account->id] ?? 0;
        if ($errors >= self::MAX_ERRORS) {
            $wait = min(pow(2, $errors - self::MAX_ERRORS + 1), 20);
            $this->warn("  Backoff: cuenta con {$errors} errores, espera {$wait} ciclos.");
            $this->errorCount[$account->id] = max(0, $errors - 1);
            return;
        }

        $connector = null;

        try {
            // Reutilizar conexiones para evitar Rate Limit (Tarpit) de Google
            static $activeConnectors = [];

            if (!isset($activeConnectors[$account->id])) {
                $connector = new ImapConnector($account, self::IMAP_TIMEOUT);
                $connector->connect();
                $activeConnectors[$account->id] = $connector;
            } else {
                $connector = $activeConnectors[$account->id];
                // Si la conexion murio, reconectar
                if (!$connector->isConnected()) {
                    $connector->connect();
                }
            }

            if (!$connector->isConnected()) {
                throw new \RuntimeException('Fallo al establecer conexion con Webklex');
            }

            $this->line('  -> Conectado OK');

            $messages = $connector->getRecentEmails(self::LOOKBACK_HOURS);

            if (empty($messages)) {
                $this->line("  -> Correos sin leer: 0");
                return;
            }

            $this->line("  -> Correos sin leer: " . count($messages));

            $messagesToProcess = array_slice($messages, 0, 20);

            $this->line("  -> Procesando los " . count($messagesToProcess) . " más recientes...");

            foreach ($messagesToProcess as $message) {
                $this->processEmail($connector, $account, $message, $platforms, $platformSubjects);
            }
            $this->errorCount[$account->id] = 0;

        } catch (\Throwable $e) {
            $this->errorCount[$account->id] = $errors + 1;
            $this->error("  Error en {$account->email}: " . $e->getMessage());
            Log::warning('[Centinela] Error procesando cuenta', [
                'account' => $account->email,
                'error'   => $e->getMessage(),
            ]);
            try { $connector?->disconnect(); } catch (\Throwable $ex) {}
        }
    }

    private function processEmail(
        ImapConnector $connector,
        EmailAccount  $account,
        $message,
        $platforms,
        array         $platformSubjects
    ): void {
        $uid = $message->getUid();

        if (ExtractedCode::where('uid', (string) $uid)
                          ->where('email_account_id', $account->id)
                          ->exists()) {
            return;
        }

        // Cargar y limpiar (minusculas sin espacios) TODOS los correos de AllowedEmail
        $expectedRecipients = \App\Models\AllowedEmail::pluck('email')
            ->map(function ($email) {
                return strtolower(trim($email));
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Buscar email, validar destinatarios y asunto con Webklex usando lazy loading
        $emailData = $connector->searchByTo($message, $expectedRecipients, $platformSubjects);

        if (!$emailData) {
            $this->line("  -> UID {$uid} | Omitido (No coincide destinatario/asunto)");
            $connector->markAsRead($uid);
            return;
        }

        $toEmail = $emailData['to'];
        $subject = $emailData['subject'];
        $body    = $emailData['body'];
        
        // La plataforma YA fue filtrada e identificada inteligentemente por searchByTo
        $matchedPlatformName = $emailData['platform_name'] ?? null;
        $matchedPlatform = $platforms->firstWhere('name', $matchedPlatformName);

        if (!$matchedPlatform) {
            $this->line("  -> UID {$uid} | Error: Plataforma no encontrada en BD ($matchedPlatformName)");
            $connector->markAsRead($uid);
            return;
        }

        $this->line("  -> UID {$uid} | Para: {$toEmail} | Asunto: " . mb_substr($subject, 0, 50));
        $this->line("  -> Plataforma: {$matchedPlatform->name}");

        $cleanText = strip_tags($body);
        $extractedData  = EmailCodeExtractor::extract($body, $cleanText);
        $extractedValue = is_array($extractedData) ? ($extractedData['value'] ?? null) : $extractedData;
        $extractedType  = is_array($extractedData) ? ($extractedData['type']  ?? 'code') : 'code';

        if (!$extractedValue) {
            $this->warn("  -> No se pudo extraer codigo del UID {$uid}");
            Log::info('[Centinela] Sin codigo extraible', [
                'uid' => $uid,
                'subject' => $subject,
                'platform' => $matchedPlatform->name
            ]);
            $connector->markAsRead($uid);
            return;
        }

        $codigoCorto = mb_substr(strip_tags($extractedValue), 0, 100);
        $this->line("  -> Tipo: {$extractedType} | Valor: {$codigoCorto}");

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

        $this->info("  -> Codigo guardado OK: {$codigoCorto} para {$toEmail} ({$matchedPlatform->name})");

        Log::info('[Centinela] Codigo capturado', [
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

    private function matchPlatform(string $subjectNorm, $platforms, array $platformSubjects): ?object
    {
        foreach ($platforms as $platform) {
            $patterns = $platformSubjects[$platform->id] ?? [];
            foreach ($patterns as $pattern) {
                if ($pattern !== '' && stripos($subjectNorm, $pattern) !== false) {
                    return $platform;
                }
            }
        }
        return null;
    }

    private function normalize(string $text): string
    {
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                return strtolower($converted);
            }
        }
        $map = [
            'a'=>'a','a'=>'a','a'=>'a','a'=>'a',
            'e'=>'e','e'=>'e','e'=>'e','e'=>'e',
            'i'=>'i','i'=>'i','i'=>'i','i'=>'i',
            'o'=>'o','o'=>'o','o'=>'o','o'=>'o',
            'u'=>'u','u'=>'u','u'=>'u','u'=>'u',
            'n'=>'n','A'=>'a','E'=>'e','I'=>'i',
            'O'=>'o','U'=>'u','N'=>'n',
        ];
        return strtolower(strtr($text, $map));
    }
}
