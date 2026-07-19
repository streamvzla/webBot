<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\EmailAccount;
use App\Models\Platform;
use App\Models\PlatformSubject;
use App\Models\Query;
use App\Services\CodeExtractor;
use App\Services\ImapConnector;
use App\Services\QueryLimiter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

/**
 * ProcessCodeRequest - Job para verificación IMAP en cola
 *
 * ⚠️ Este job maneja operaciones IMAP que pueden tardar.
 * Se ejecuta en cola para no bloquear la UI del cliente.
 */
class ProcessCodeRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $maxExceptions = 3;
    public int $timeout = 30;

    protected Client $client;
    protected Platform $platform;
    protected string $ipAddress;
    protected string $userAgent;

    /**
     * Create a new job instance.
     */
    public function __construct(Client $client, Platform $platform, string $ipAddress = '', string $userAgent = '')
    {
        $this->client = $client;
        $this->platform = $platform;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = microtime(true);

        Log::info('Iniciando procesamiento de código', [
            'client_id' => $this->client->id,
            'platform' => $this->platform->name,
        ]);

        try {
            // Obtener cuenta de correo asociada al cliente
            $emailAccount = $this->client->emailAccount;

            if (!$emailAccount) {
                $this->logQuery('error', null, 'No hay cuenta de correo asignada');
                return;
            }

            // Desencriptar contraseña IMAP
            try {
                $imapPassword = Crypt::decryptString($emailAccount->imap_password);
            } catch (\Exception $e) {
                Log::error('Error al desencriptar contraseña IMAP', [
                    'client_id' => $this->client->id,
                    'error' => $e->getMessage(),
                ]);
                $this->logQuery('error', null, 'Error de configuración de cuenta de correo');
                return;
            }

            // Conectar a IMAP
            $connector = new ImapConnector($emailAccount);
            $connector->connect();

            // Obtener subjects de la plataforma
            $subjects = PlatformSubject::where('platform_id', $this->platform->id)
                ->where('is_active', true)
                ->pluck('subject')
                ->toArray();

            if (empty($subjects)) {
                $this->logQuery('error', null, 'No hay subjects configurados para esta plataforma');
                $connector->disconnect();
                return;
            }

            $foundCode = null;

            // Buscar en cada asunto configurado
            foreach ($subjects as $subject) {
                $emails = $connector->searchBySubject($subject);

                if (!empty($emails)) {
                    Log::info('Emails encontrados para subject', [
                        'subject' => $subject,
                        'count' => count($emails),
                    ]);

                    // Obtener el email más reciente
                    $latestEmail = max($emails);
                    $body = $connector->getEmailBody($latestEmail);

                    // Extraer código
                    $code = CodeExtractor::extract($body, strtolower($this->platform->slug));

                    if ($code) {
                        $foundCode = $code;
                        Log::info('Código encontrado', [
                            'code' => CodeExtractor::maskCode($code),
                            'subject' => $subject,
                        ]);
                        break;
                    }
                }
            }

            $connector->disconnect();

            // Registrar resultado
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($foundCode) {
                // Guardar hash del código (nunca el código completo)
                $codeHash = Query::hashCode($foundCode);
                $this->logQuery('success', $codeHash, 'Código encontrado', $responseTime);

                // El código se muestra directamente al cliente, no se guarda en BD
                // La respuesta se maneja en el controlador que despachó este job
            } else {
                $this->logQuery('no_code', null, 'No se encontró código reciente', $responseTime);
            }

            // Actualizar contador del cliente
            $limiter = new QueryLimiter($this->client);
            $limiter->recordQuery();

        } catch (\Exception $e) {
            Log::error('Error en ProcessCodeRequest', [
                'client_id' => $this->client->id,
                'platform' => $this->platform->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->logQuery('error', null, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Log de la consulta
     */
    protected function logQuery(string $result, ?string $codeHash, string $message, float $responseTime = 0): void
    {
        Query::create([
            'client_id' => $this->client->id,
            'email_account_id' => $this->client->email_account_id,
            'platform_id' => $this->platform->id,
            'email' => $this->client->emailAccount?->email ?? $this->client->email,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'result' => $result,
            'code_hash' => $codeHash,
            'code_status' => $result === 'success' ? 'delivered' : 'pending',
            'response_time' => $responseTime,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessCodeRequest job failed', [
            'client_id' => $this->client->id,
            'platform' => $this->platform->name,
            'error' => $exception->getMessage(),
        ]);

        $this->logQuery('error', null, 'Job falló: ' . $exception->getMessage());
    }
}
