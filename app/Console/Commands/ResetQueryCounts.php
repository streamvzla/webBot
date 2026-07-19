<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class ResetQueryCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queries:reset-counts {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily query counts for all active clients';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');

        if (!$force && !$this->confirm('¿Estás seguro de resetear los contadores de consultas de todos los clientes?')) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }

        $resetCount = Client::where('query_count', '>', 0)->update(['query_count' => 0]);

        $this->info("Se han reseteado {$resetCount} contador(es) de consultas.");

        return Command::SUCCESS;
    }
}
