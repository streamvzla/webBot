<?php
/**
 * Script de diagnóstico para ver destinatarios (TO) de correos en la bandeja
 * USO: Acceder desde navegador: http://tu-proyecto.test/diagnose_to_fields.php
 */

// Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ImapConnector;
use App\Models\EmailAccount;

// Colores para consola
define('GREEN', "\033[32m");
define('RED', "\033[31m");
define('YELLOW', "\033[33m");
define('RESET', "\033[0m");

// Headers HTML para navegador
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico - Campos TO de Correos</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        h1 { color: #569cd6; }
        h2 { color: #4ec9b0; margin-top: 30px; }
        .email { background: #2d2d2d; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #569cd6; }
        .subject { color: #dcdcaa; font-size: 14px; }
        .to { color: #9cdcfe; font-size: 13px; margin-top: 5px; }
        .date { color: #6a9955; font-size: 12px; }
        .uid { color: #ce9178; font-size: 11px; }
        .error { background: #3d1e1e; border-left-color: #f14c4c; }
        .success { background: #1e3d2d; border-left-color: #4ec9b0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #3d3d3d; }
        th { background: #2d2d2d; color: #569cd6; }
        .highlight { background: #3d3d1e; }
    </style>
</head>
<body>
<h1>📧 Diagnóstico - Campos TO de Correos</h1>
";

try {
    // Buscar la cuenta de correo info@devhubve.com
    $emailAccount = EmailAccount::where('email', 'info@devhubve.com')->first();

    if (!$emailAccount) {
        echo "<p style='color: #f14c4c;'>❌ No se encontró la cuenta de correo: info@devgubve.com</p>";
        echo "<p> Cuentas disponibles en la BD:</p>";
        $accounts = EmailAccount::all();
        if ($accounts->count() > 0) {
            echo "<ul>";
            foreach ($accounts as $acc) {
                echo "<li>" . htmlspecialchars($acc->email) . " - " . ($acc->active ? 'Activo' : 'Inactivo') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No hay cuentas configuradas.</p>";
        }
        exit;
    }

    echo "<p>✅ <strong>Cuenta encontrada:</strong> " . $emailAccount->email . "</p>";
    echo "<p><strong>Servidor:</strong> " . $emailAccount->imap_host . ":" . $emailAccount->imap_port . "</p>";

    // Conectar al IMAP
    $connector = new ImapConnector($emailAccount);
    $connection = $connector->connect();

    if (!$connection) {
        echo "<p class='error'>❌ No se pudo conectar al servidor IMAP</p>";
        exit;
    }

    echo "<p class='success'>✅ Conexión IMAP exitosa</p>";

    // Obtener todos los asuntos configurados para buscar
    $subjects = \App\Models\PlatformSubject::with('platform')->get();

    echo "<h2>📋 Correos encontrados por asunto:</h2>";

    foreach ($subjects as $subjectConfig) {
        $platformName = $subjectConfig->platform->name ?? 'Desconocido';
        $subjectPattern = $subjectConfig->subject_pattern;

        // Saltar si no hay patrón de asunto
        if (empty($subjectPattern)) {
            continue;
        }

        echo "<h3>🔍 Plataforma: " . htmlspecialchars($platformName) . " - Patrón: " . htmlspecialchars($subjectPattern) . "</h3>";

        // Buscar correos con este subject
        $emails = $connector->searchBySubject($subjectPattern);

        if (empty($emails)) {
            echo "<p style='color: #6a9955;'>  No se encontraron correos con este patrón</p>";
            continue;
        }

        echo "<table>";
        echo "<tr><th>UID</th><th>Fecha</th><th>Subject</th><th>TO (Destinatario)</th></tr>";

        foreach ($emails as $uid) {
            $overview = @imap_fetch_overview($connector->getConnection(), $uid, FT_UID);
            if (!$overview || !isset($overview[0])) continue;

            $email = $overview[0];

            // Decodificar subject (puede venir en formato MIME)
            $subjectRaw = $email->subject ?? '';
            $subjectDecoded = imap_utf8($subjectRaw);

            // Extraer email del campo TO
            $toRaw = $email->to ?? '';
            $toAddress = '';
            if (!empty($toRaw)) {
                // El campo to puede venir en varios formatos
                if (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $toRaw, $matches)) {
                    $toAddress = $matches[1];
                } elseif (filter_var($toRaw, FILTER_VALIDATE_EMAIL)) {
                    $toAddress = $toRaw;
                } else {
                    // Si no podemos extraer email válido, mostrar el raw
                    $toAddress = $toRaw;
                }
            }

            // Resaltar si contiene ricardojguerrac@gmail.com
            $highlight = (stripos(strtolower($toAddress), 'ricardojguerrac@gmail.com') !== false) ? ' class="highlight"' : '';

            echo "<tr$highlight>";
            echo "<td>" . $uid . "</td>";
            echo "<td>" . htmlspecialchars($email->date ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($subjectDecoded) . "</td>";
            echo "<td>" . htmlspecialchars($toAddress) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }

    // También mostrar últimos 10 correos sin filtrar
    echo "<h2>📬 Últimos 10 correos en bandeja (sin filtro):</h2>";
    echo "<table>";
    echo "<tr><th>UID</th><th>Fecha</th><th>Subject</th><th>TO (Destinatario)</th></tr>";

    $recentEmails = $connector->getRecentEmails(24); // Últimas 24 horas
    foreach ($recentEmails as $uid) {
        $overview = @imap_fetch_overview($connector->getConnection(), $uid, FT_UID);
        if (!$overview || !isset($overview[0])) continue;

        $email = $overview[0];

        // Decodificar subject
        $subjectRaw = $email->subject ?? '';
        $subjectDecoded = imap_utf8($subjectRaw);

        // Extraer email del campo TO
        $toRaw = $email->to ?? '';
        $toAddress = '';
        if (!empty($toRaw)) {
            if (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $toRaw, $matches)) {
                $toAddress = $matches[1];
            } elseif (filter_var($toRaw, FILTER_VALIDATE_EMAIL)) {
                $toAddress = $toRaw;
            } else {
                $toAddress = $toRaw;
            }
        }

        $highlight = (stripos(strtolower($toAddress), 'ricardojguerrac@gmail.com') !== false) ? ' class="highlight"' : '';

        echo "<tr$highlight>";
        echo "<td>" . $uid . "</td>";
        echo "<td>" . htmlspecialchars($email->date ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($subjectDecoded) . "</td>";
        echo "<td>" . htmlspecialchars($toAddress) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Cerrar conexión
    imap_close($connector->getConnection());

} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
