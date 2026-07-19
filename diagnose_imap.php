<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Diagnóstico de Conexión IMAP ===\n\n";

// Obtener la cuenta de correo
$emailAccount = DB::table('email_accounts')->where('email', 'info@devhubve.com')->first();

if (!$emailAccount) {
    echo "ERROR: No se encontró la cuenta info@devhubve.com\n";
    exit(1);
}

echo "Cuenta encontrada: {$emailAccount->email}\n";
echo "Host: {$emailAccount->imap_host}\n";
echo "Puerto: {$emailAccount->imap_port}\n";
echo "Encriptación: {$emailAccount->imap_encryption}\n";
echo "Username: {$emailAccount->username}\n\n";

// Verificar si la extensión IMAP está cargada
echo "=== Verificación de Extensiones PHP ===\n";
echo "Extensión IMAP: " . (extension_loaded('imap') ? 'Cargada' : 'NO CARGADA') . "\n";
echo "Extensión OpenSSL: " . (extension_loaded('openssl') ? 'Cargada' : 'NO CARGADA') . "\n\n";

// Obtener contraseña
echo "=== Contraseña ===\n";
$password = $emailAccount->imap_password;
echo "Campo imap_password existe: " . (!empty($password) ? 'Sí' : 'No') . "\n";

if (!empty($password)) {
    try {
        $decrypted = Crypt::decryptString($password);
        echo "Contraseña desencriptada: [OCULTA - longitud: " . strlen($decrypted) . "]\n";
        $plainPassword = $decrypted;
    } catch (\Exception $e) {
        echo "La contraseña no está encriptada, usando texto plano\n";
        $plainPassword = $password;
    }
} else {
    echo "ERROR: Campo imap_password está vacío\n";
    exit(1);
}

/**
 * Obtener flags SSL apropiados según el servidor
 */
function getSslFlags(string $host): string
{
    // Detectar Gmail - requiere validación de certificado
    if (strpos($host, 'gmail.com') !== false ||
        strpos($host, 'googlemail.com') !== false) {
        echo "⚠️  Servidor Gmail detectado, usando validación de certificado\n";
        return '/imap/ssl/validate-cert';
    }

    echo "ℹ️  Servidor privado detectado, usando novalidate-cert\n";
    return '/imap/ssl/novalidate-cert';
}

echo "\n=== Probando Conexión IMAP ===\n";

// Limpiar errores previos
imap_errors();

// Construir mailbox string
$host = $emailAccount->imap_host;
$port = $emailAccount->imap_port;
$encryption = $emailAccount->imap_encryption ?? 'ssl';

$flags = getSslFlags($host);

$mailbox = "{{$host}:{$port}{$flags}}";
echo "Mailbox: {$mailbox}\n";
echo "Username para login: {$emailAccount->username}\n\n";

// Probar conexión
echo "Intentando conectar...\n";
$connection = @imap_open(
    $mailbox,
    $emailAccount->username,
    $plainPassword,
    OP_READONLY,
    1,
    ['DISABLE_AUTHENTICATOR' => ['GSSAPI', 'NTLM']]
);

if ($connection) {
    echo "✓ CONEXIÓN EXITOSA\n";

    // Obtener información del buzón
    $mailboxInfo = imap_mailboxmsginfo($connection);
    if ($mailboxInfo) {
        echo "Mensajes en buzón: {$mailboxInfo->Nmsgs}\n";
    }

    imap_close($connection);
} else {
    $error = imap_last_error();
    $allErrors = imap_errors();
    echo "✗ CONEXIÓN FALLIDA\n";
    echo "Error: {$error}\n";
    if ($allErrors) {
        echo "Todos los errores:\n";
        print_r($allErrors);
    }
}

echo "\n=== Fin del diagnóstico ===\n";
