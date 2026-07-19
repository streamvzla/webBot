<?php
// Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Crypt;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Test IMAP Completo desde Web ===\n\n";

// Buscar la cuenta de email configurada
$emailAccount = EmailAccount::where('email', 'info@devhubve.com')->first();

if (!$emailAccount) {
    echo "ERROR: No se encontro la cuenta info@devhubve.com en la base de datos.\n";
    exit(1);
}

$host = $emailAccount->imap_host;
$port = $emailAccount->imap_port;
$encryption = $emailAccount->imap_encryption;
$email = $emailAccount->email;

// Obtener la contraseña (desencriptar si es necesario)
$password = $emailAccount->imap_password;
try {
    $password = Crypt::decryptString($password);
    echo "0. Contrasena: (desencriptada correctamente)\n";
} catch (\Exception $e) {
    echo "0. Contrasena: ( texto plano)\n";
}

/**
 * Obtener flags SSL apropiados según el servidor
 */
function getSslFlags(string $host): string
{
    // Detectar Gmail - requiere validación de certificado
    if (strpos($host, 'gmail.com') !== false ||
        strpos($host, 'googlemail.com') !== false) {
        echo "⚠️  Servidor Gmail detectado, usando validacion de certificado\n";
        return '/imap/ssl/validate-cert';
    }

    echo "ℹ️  Servidor privado detectado, usando novalidate-cert\n";
    return '/imap/ssl/novalidate-cert';
}

$flags = getSslFlags($host);
$mailbox = "{{$host}:{$port}{$flags}}";

echo "1. Parametros de conexion:\n";
echo "   Mailbox: {$mailbox}\n";
echo "   Username: {$email}\n";
echo "   Password length: " . strlen($password) . " caracteres\n\n";

echo "2. Probando imap_open...\n";
$start = microtime(true);

// Intentar conectar con imap_open (como lo hace ImapConnector)
$connection = @imap_open(
    $mailbox,
    $email,
    $password,
    OP_READONLY,
    1,
    [
        'DISABLE_AUTHENTICATOR' => ['GSSAPI', 'NTLM'],
    ]
);

$time = round((microtime(true) - $start) * 1000, 2);

if ($connection) {
    echo "   CONEXION EXITOSA ({$time}ms)\n";

    // Obtener informacion del buzon
    $mailboxInfo = imap_mailboxmsginfo($connection);
    if ($mailboxInfo) {
        echo "   Mensajes en buzon: {$mailboxInfo->Nmsgs}\n";
    }

    imap_close($connection, CL_EXPUNGE);
    echo "   La conexion IMAP funciona correctamente.\n";
} else {
    $error = imap_last_error();
    $errors = imap_errors();
    echo "   ERROR ({$time}ms): {$error}\n";
    if ($errors) {
        echo "   Todos los errores: " . print_r($errors, true) . "\n";
    }
    echo "   La funcion imap_open falla.\n";
}

echo "\n3. PHP Info:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   Extension IMAP: " . (extension_loaded('imap') ? 'Cargada' : 'No cargada') . "\n";
