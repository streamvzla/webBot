<?php
// Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmailAccount;
use App\Services\ImapConnector;
use Illuminate\Support\Facades\Log;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Test: Reproducir diagnoseImap() exactamente ===\n\n";

// Configurar Log para ver todo
Log::setDefaultDriver('stack');

echo "1. Buscando primera cuenta activa...\n";
$emailAccount = EmailAccount::where('is_active', true)->first();

if (!$emailAccount) {
    echo "   ERROR: No hay cuentas activas\n";
    exit(1);
}

echo "   Cuenta encontrada: {$emailAccount->email}\n\n";

$diagnostics = [
    'account_found' => true,
    'email' => $emailAccount->email,
    'host' => $emailAccount->imap_host,
    'port' => $emailAccount->imap_port,
    'encryption' => $emailAccount->imap_encryption,
    'username' => $emailAccount->username,
    'password_configured' => !empty($emailAccount->imap_password),
];

echo "2. Diagnostico basico:\n";
echo "   - Email: {$diagnostics['email']}\n";
echo "   - Host: {$diagnostics['host']}:{$diagnostics['port']}\n";
echo "   - Encryption: {$diagnostics['encryption']}\n";
echo "   - Username: {$diagnostics['username']}\n";
echo "   - Password configured: " . ($diagnostics['password_configured'] ? 'Si' : 'No') . "\n\n";

try {
    echo "3. Probando puerto IMAP...\n";
    $portTest = ImapConnector::testConnection(
        $emailAccount->imap_host,
        $emailAccount->imap_port,
        $emailAccount->imap_encryption
    );
    $diagnostics['port_accessible'] = $portTest['success'];
    $diagnostics['port_test_message'] = $portTest['message'];
    echo "   Resultado: " . ($portTest['success'] ? 'ACCESIBLE' : 'NO ACCESIBLE') . "\n";
    echo "   Mensaje: {$portTest['message']}\n\n";

    if (!$portTest['success']) {
        throw new \Exception("Puerto no accesible: {$portTest['message']}");
    }

    echo "4. Conectando a IMAP (como lo hace diagnoseImap)...\n";
    $connector = new ImapConnector($emailAccount);

    echo "   Creando conector...\n";
    echo "   Intentando connect()...\n";

    $start = microtime(true);
    $connector->connect();
    $time = round((microtime(true) - $start) * 1000, 2);

    echo "   ✅ CONEXION EXITOSA ({$time}ms)\n\n";

    echo "5. Obteniendo estado de conexion...\n";
    $connectionStatus = $connector->getConnectionStatus();
    $diagnostics['connection'] = $connectionStatus;
    echo "   Has connection: " . ($connectionStatus['has_connection'] ? 'Si' : 'No') . "\n";
    echo "   Is connected: " . ($connectionStatus['is_connected'] ? 'Si' : 'No') . "\n";

    if (isset($connectionStatus['mailbox_info'])) {
        $info = $connectionStatus['mailbox_info'];
        echo "   Mensajes: {$info['messages']}\n";
    }

    echo "\n6. Obteniendo mailboxes...\n";
    $mailboxes = $connector->getMailboxes();
    $diagnostics['mailboxes'] = $mailboxes;
    echo "   Mailboxes encontrados: " . count($mailboxes) . "\n\n";

    echo "7. Desconectando...\n";
    $connector->disconnect();
    echo "   ✅ Desconectado\n\n";

    echo "=== RESULTADO: TODO EXITOSO ===\n";
    echo "El metodo diagnoseImap() deberia funcionar correctamente.\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR CAPTURADO:\n";
    echo "   Tipo: " . get_class($e) . "\n";
    echo "   Mensaje: {$e->getMessage()}\n";
    echo "   Archivo: {$e->getFile()}:{$e->getLine()}\n\n";

    echo "=== RESULTADO: ERROR ===\n";
    echo "El metodo diagnoseImap() fallaria con este error.\n";
}
