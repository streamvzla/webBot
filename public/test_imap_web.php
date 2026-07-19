<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== Test IMAP desde Web ===\n\n";

// Verificar extensiones
echo "1. Extension IMAP cargada: " . (extension_loaded('imap') ? 'SI' : 'NO') . "\n";
echo "2. Extension OpenSSL cargada: " . (extension_loaded('openssl') ? 'SI' : 'NO') . "\n";

// Verificar funciones
echo "3. fsockopen disponible: " . (function_exists('fsockopen') ? 'SI' : 'NO') . "\n";
echo "4. imap_open disponible: " . (function_exists('imap_open') ? 'SI' : 'NO') . "\n";

// Probar conexion directa con fsockopen
echo "\n5. Probando fsockopen a imap.hostinger.com:993...\n";
$start = microtime(true);
$socket = @fsockopen('ssl://imap.hostinger.com', 993, $errno, $errstr, 10);
$time = round((microtime(true) - $start) * 1000, 2);

if ($socket) {
    echo "   CONEXION EXITOSA ({$time}ms)\n";
    fclose($socket);
    echo "   El puerto 993 ES ACCESIBLE desde el servidor web.\n";
    echo "   El problema esta en la configuracion PHP/Apache, no en la red.\n";
} else {
    echo "   ERROR: {$errstr} ({$errno})\n";
    echo "   Tiempo de espera: {$time}ms\n";
    echo "   El puerto 993 NO es accesible.\n";
}

// Verificar php.ini
echo "\n6. PHP info:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   Loaded php.ini: " . (php_ini_loaded_file() ?: 'N/A') . "\n";
