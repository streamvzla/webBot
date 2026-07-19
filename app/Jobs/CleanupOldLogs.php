<?php

namespace App\Jobs;

use App\Models\Query;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * CleanupOldLogs - Job para limpiar logs antiguos
 *
 * ⚠️ CRÍTICO: Nunca almacenar códigos completos en logs
 * Este job elimina logs antiguos según la retención configurada.
 *
 * Programar en Kernel.php:
 * $schedule->job(new CleanupOldLogs())->daily();
 */
class CleanupOldLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 60;

    protected int $retentionDays;

    /**
     * Create a new job instance.
     */
    public function __construct(?int $retentionDays = null)
    {
        $this->retentionDays = $retentionDays ?? (int) config('app.log_retention_days', 30);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando limpieza de logs antiguos', [
            'retention_days' => $this->retentionDays,
        ]);

        $deletedCount = Query::where('created_at', '<', now()->subDays($this->retentionDays))
            ->delete();

        Log::info('Limpieza de logs completada', [
            'deleted_count' => $deletedCount,
            'retention_days' => $this->retentionDays,
        ]);

        // También limpiar consultas sin código (no_code) después de 7 días
        $cleanupNoCode = Query::where('result', 'no_code')
            ->where('created_at', '<', now()->subDays(7))
            ->delete();

        Log::info('Limpieza de consultas sin código completada', [
            'deleted_count' => $cleanupNoCode,
        ]);
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupOldLogs job failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
