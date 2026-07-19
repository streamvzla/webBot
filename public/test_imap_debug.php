<?php
// Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Crypt;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Debug: Todas las cuentas de email ===\n\n";

$accounts = EmailAccount::where('is_active', true)->get();

echo "Cuentas activas encontradas: " . $accounts->count() . "\n\n";

/**
 * Obtener flags SSL apropiados según el servidor
 */
function getSslFlags(string $host): string
{
    // Detectar Gmail - requiere validación de certificado
    if (strpos($host, 'gmail.com') !== false ||
        strpos($host, 'googlemail.com') !== false) {
        return '/imap/ssl/validate-cert';
    }

    // Servidores privados pueden usar novalidate-cert
    return '/imap/ssl/novalidate-cert';
}

foreach ($accounts as $index => $account) {
    echo "--- Cuenta #" . ($index + 1) . " ---\n";
    echo "ID: {$account->id}\n";
    echo "Email: {$account->email}\n";
    echo "Host: {$account->imap_host}:{$account->imap_port}\n";
    echo "Encryption: {$account->imap_encryption}\n";
    echo "Username: {$account->username}\n";
    echo "Password length: " . strlen($account->imap_password) . " caracteres\n";

    // Intentar desencriptar
    try {
        $decrypted = Crypt::decryptString($account->imap_password);
        echo "Password decrypt: EXITOSA (" . strlen($decrypted) . " chars)\n";
    } catch (\Exception $e) {
        echo "Password decrypt: FALLA (texto plano)\n";
        $decrypted = $account->imap_password;
    }

    // Determinar flags según el servidor
    $flags = getSslFlags($account->imap_host);
    $mailbox = "{{$account->imap_host}:{$account->imap_port}{$flags}}";
    echo "Mailbox: {$mailbox}\n";

    echo "Conectando...\n";
    $start = microtime(true);

    $connection = @imap_open(
        $mailbox,
        $account->email,
        $decrypted,
        OP_READONLY,
        1,
        [
            'DISABLE_AUTHENTICATOR' => ['GSSAPI', 'NTLM'],
        ]
    );

    $time = round((microtime(true) - $start) * 1000, 2);

    if ($connection) {
        echo "   ✅ CONEXION EXITOSA ({$time}ms)\n";
        $mailboxInfo = imap_mailboxmsginfo($connection);
        if ($mailboxInfo) {
            echo "   Mensajes: {$mailboxInfo->Nmsgs}\n";
        }
        imap_close($connection, CL_EXPUNGE);
    } else {
        echo "   ❌ ERROR ({$time}ms): " . imap_last_error() . "\n";
    }

    echo "\n";
}

echo "=== Fin del debug ===\n";
