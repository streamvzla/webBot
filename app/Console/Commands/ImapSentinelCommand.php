<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailAccount;
use App\Models\Platform;
use App\Models\ExtractedCode;
use App\Jobs\ProcessImapAccountJob;
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

        if ($accounts->isEmpty()) {
            $this->warn('No hay cuentas activas. Esperando...');
            return;
        }

        $this->info("Despachando clones (Workers) para " . count($accounts) . " cuenta(s)...");

        foreach ($accounts as $account) {
            // Despachar el Job para que los Workers paralelos lo procesen.
            // Si ya hay un Job corriendo para esta cuenta, el middleware WithoutOverlapping lo omitirá.
            ProcessImapAccountJob::dispatch($account);
        }
        
        $this->line("Clones despachados exitosamente.");
    }
}
