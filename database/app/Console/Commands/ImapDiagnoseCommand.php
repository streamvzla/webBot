<?php

namespace App\Console\Commands;

use App\Models\EmailAccount;
use App\Services\ImapConnector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ImapDiagnoseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imap:diagnose {email? : Email account to diagnose} {--all : Diagnose all active email accounts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose IMAP connection for email accounts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $diagnoseAll = $this->option('all');

        if (!$email && !$diagnoseAll) {
            $this->error('Please provide an email address or use --all to diagnose all accounts');
            $this->info('Usage:');
            $this->info('  php artisan imap:diagnose user@example.com');
            $this->info('  php artisan imap:diagnose --all');
            return Command::FAILURE;
        }

        if ($diagnoseAll) {
            return $this->diagnoseAllAccounts();
        }

        return $this->diagnoseSingleAccount($email);
    }

    /**
     * Diagnose a single email account.
     */
    private function diagnoseSingleAccount(string $email): int
    {
        $this->info("=== Diagnosing IMAP connection for: {$email} ===\n");

        $emailAccount = EmailAccount::where('email', $email)
            ->where('is_active', true)
            ->first();

        if (!$emailAccount) {
            $this->error("Email account not found or inactive: {$email}");

            // Try to find even if inactive
            $emailAccount = EmailAccount::where('email', $email)->first();
            if ($emailAccount) {
                $this->warn("Account exists but is inactive");
            }
            return Command::FAILURE;
        }

        return $this->performDiagnosis($emailAccount);
    }

    /**
     * Diagnose all active email accounts.
     */
    private function diagnoseAllAccounts(): int
    {
        $this->info("=== Diagnosing all active IMAP accounts ===\n");

        $accounts = EmailAccount::where('is_active', true)->get();

        if ($accounts->isEmpty()) {
            $this->warn('No active email accounts found');
            return Command::FAILURE;
        }

        $results = [];
        foreach ($accounts as $account) {
            $this->info("Testing: {$account->email}");
            $result = $this->performDiagnosis($account, false);
            $results[] = [
                'email' => $account->email,
                'success' => $result === Command::SUCCESS,
            ];
            $this->newLine();
        }

        $successCount = collect($results)->where('success', true)->count();
        $failCount = collect($results)->where('success', false)->count();

        $this->info("=== Summary ===");
        $this->info("Total accounts: " . count($results));
        $this->info("Successful: {$successCount}");
        $this->error("Failed: {$failCount}");

        return $failCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Perform full diagnosis of an email account.
     */
    private function performDiagnosis(EmailAccount $emailAccount, bool $verbose = true): int
    {
        $host = $emailAccount->imap_host;
        $port = $emailAccount->imap_port ?? 993;
        $encryption = $emailAccount->imap_encryption ?? 'ssl';

        // Step 1: Check configuration
        if ($verbose) {
            $this->info('1. Checking configuration...');
        }

        $config = [
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $emailAccount->username,
            'password_set' => !empty($emailAccount->imap_password),
        ];

        if ($verbose) {
            $this->table(['Setting', 'Value'], [
                ['Host', $host],
                ['Port', $port],
                ['Encryption', $encryption],
                ['Username', $emailAccount->username],
                ['Password Configured', $config['password_set'] ? 'Yes' : 'No'],
            ]);
        }

        // Step 2: Test port connectivity
        if ($verbose) {
            $this->info('2. Testing port connectivity...');
        }

        $portTest = ImapConnector::testConnection($host, $port, $encryption);

        if ($verbose) {
            $this->line($portTest['message']);
        }

        if (!$portTest['success']) {
            $this->error("   ✗ Port {$port} is not accessible");
            if ($verbose) {
                $this->error("   Error: {$portTest['message']}");
            }
            return Command::FAILURE;
        }
        $this->info("   ✓ Port {$port} is accessible");

        // Step 3: Test IMAP connection
        if ($verbose) {
            $this->info('3. Testing IMAP connection...');
        }

        try {
            // Get password - try to decrypt first, then use as plain text
            $password = $emailAccount->imap_password;
            try {
                $password = Crypt::decryptString($emailAccount->imap_password);
            } catch (\Exception $e) {
                // Password is not encrypted, use as plain text
                $password = $emailAccount->imap_password;
                if ($verbose) {
                    $this->line('   (Password is plain text)');
                }
            }

            // Usar conexión IMAP directa para evitar el conflicto con el shutdown de Laravel
            $host = $emailAccount->imap_host;
            $port = $emailAccount->imap_port ?? 993;
            $encryption = $emailAccount->imap_encryption ?? 'ssl';

            $flags = match ($encryption) {
                'ssl' => '/imap/ssl/novalidate-cert',
                'tls' => '/imap/tls/novalidate-cert',
                default => '/imap/ssl/novalidate-cert',
            };

            $mailbox = "{{$host}:{$port}{$flags}}";

            // Deshabilitar temporalmente el manejador de errores de Laravel
            $handler = null;
            if (class_exists('\Illuminate\Foundation\Bootstrap\HandleExceptions')) {
                // Backup y deshabilitar el manejador de errores de Laravel
                $handler = set_error_handler(function($errno, $errstr, $errfile, $errline) {
                    if (stripos($errstr, 'Login aborted') !== false || stripos($errstr, 'imap') !== false) {
                        return true;
                    }
                    return false;
                });
            }

            // Limpiar errores IMAP
            @imap_errors();

            // Conectar directamente con imap_open
            $connection = @imap_open($mailbox, $emailAccount->username, $password, OP_READONLY, 1);

            // Restaurar manejador de errores
            if ($handler !== null) {
                set_error_handler($handler);
            }

            if (!$connection) {
                $error = @imap_last_error();
                throw new \Exception($error ?: 'Unknown IMAP error');
            }

            // Obtener info del buzón
            $mailboxInfo = @imap_mailboxmsginfo($connection);
            $messages = $mailboxInfo->Nmsgs ?? 0;

            // Cerrar conexión
            @imap_close($connection, CL_EXPUNGE);

            if ($verbose) {
                $this->info('4. Connection established successfully!');
                $this->info('5. Getting mailbox info...');
                $this->info("   ✓ Connected and authenticated");
                $this->info("   ✓ Messages in inbox: {$messages}");
            }

            if ($verbose) {
                $this->info('\n=== Diagnosis Complete: SUCCESS ===');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("   ✗ Connection failed: {$e->getMessage()}");

            // Log detailed error
            Log::error('IMAP Diagnosis failed', [
                'email' => $emailAccount->email,
                'host' => $host,
                'error' => $e->getMessage(),
            ]);

            if ($verbose) {
                $this->error('\n=== Diagnosis Complete: FAILED ===');
                $this->info('\nTroubleshooting tips:');
                $this->info('1. Verify host and port are correct');
                $this->info('2. Check firewall allows outbound IMAP connections');
                $this->info('3. Verify username and password are correct');
                $this->info('4. Check if 2FA is enabled on the email account');
                $this->info('5. Verify IMAP is enabled in email account settings');
            }

            return Command::FAILURE;
        }
    }
}
