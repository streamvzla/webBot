<?php
/**
 * Script de depuracion para la consulta de codigo
 * Muestra que esta pasando con el filtrado por TO y subject
 */

// Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmailAccount;
use App\Models\Platform;
use App\Services\ImapConnector;

/**
 * Eliminar acentos de una cadena (maneja UTF-8 correctamente)
 */
function removeAccents(string $string): string {
    // Primero intentamos con normalizer si esta disponible
    if (function_exists('normalizer_normalize')) {
        $string = normalizer_normalize($string, \Normalizer::FORM_D) ?? $string;
    }

    // Eliminar marcas de combinacion (diacriticos como acentos)
    $string = preg_replace('/\p{M}/u', '', $string);

    // Si aun quedan acentos, usar reemplazo manual
    $acentos = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'];
    $sinAcentos = ['a', 'e', 'i', 'o', 'u', 'n', 'u', 'A', 'E', 'I', 'O', 'U', 'N', 'U'];
    $string = str_replace($acentos, $sinAcentos, $string);

    return strtolower($string);
}

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug - Consulta de Codigo</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        h1 { color: #569cd6; }
        h2 { color: #4ec9b0; margin-top: 30px; }
        h3 { color: #dcdcaa; }
        .email { background: #2d2d2d; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .match { border-left: 4px solid #4ec9b0; }
        .no-match { border-left: 4px solid #f14c4c; }
        .to-email { color: #9cdcfe; font-weight: bold; }
        .subject { color: #dcdcaa; }
        .platform-subject { color: #ce9178; }
        .found { background: #1e3d2d; }
        .not-found { background: #3d1e1e; }
        pre { background: #2d2d2d; padding: 10px; overflow-x: auto; }
        select, input, button { padding: 8px; border-radius: 4px; border: 1px solid #444; background: #333; color: white; }
        button { background: #569cd6; cursor: pointer; }
        button:hover { background: #4a8bc4; }
    </style>
</head>
<body>
<h1>Debug - Consulta de Codigo</h1>
";

// Obtener plataformas para el dropdown
$allPlatforms = Platform::where('is_active', true)->select('id', 'name')->get();

// Obtener cuentas de correo habilitadas
$availableEmailAccounts = EmailAccount::where('is_active', true)
    ->select('id', 'email', 'imap_host')
    ->get();

// Obtener parametros de la URL
$email = $_GET['email'] ?? 'ricardojguerrac@gmail.com';
$platformId = isset($_GET['platform_id']) ? (int)$_GET['platform_id'] : ($allPlatforms->first()->id ?? 1);
$emailAccountId = isset($_GET['email_account_id']) ? (int)$_GET['email_account_id'] : ($availableEmailAccounts->first()->id ?? null);

echo '<form method="GET">';
echo '<label>Email a buscar: <input type="email" name="email" value="' . htmlspecialchars($email) . '" style="width: 300px;"></label> ';
echo '<label>Plataforma: <select name="platform_id" style="width: 200px;">;';
foreach ($allPlatforms as $p) {
    $selected = ($p->id == $platformId) ? 'selected' : '';
    echo '<option value="' . $p->id . '" ' . $selected . '>' . htmlspecialchars($p->name) . ' (ID: ' . $p->id . ')</option>';
}
echo '</select></label> ';
echo '<label>Servidor IMAP: <select name="email_account_id" style="width: 250px;">;';
foreach ($availableEmailAccounts as $acc) {
    $selected = ($acc->id == $emailAccountId) ? 'selected' : '';
    echo '<option value="' . $acc->id . '" ' . $selected . '>' . htmlspecialchars($acc->email) . ' (' . htmlspecialchars($acc->imap_host) . ')</option>';
}
echo '</select></label> ';
echo '<button type="submit">Debug</button>';
echo '</form>';

echo '<h2>Parametros:</h2>';
echo '<pre>';
echo "Email a buscar: $email\n";
echo "Platform ID: $platformId\n";
echo '</pre>';

// Buscar cuenta de correo
if (!$emailAccountId) {
    echo "<p class='not-found'>No hay servidores IMAP habilitados disponibles.</p>";
    echo "<h3>Servidores disponibles:</h3>";
    if ($availableEmailAccounts->isEmpty()) {
        echo "<p>No hay ninguna cuenta de correo habilitada.</p>";
    } else {
        echo "<ul>";
        foreach ($availableEmailAccounts as $acc) {
            echo '<li>' . htmlspecialchars($acc->email) . ' (' . htmlspecialchars($acc->imap_host) . ')</li>';
        }
        echo "</ul>";
    }
    exit;
}

$emailAccount = EmailAccount::find($emailAccountId);

if (!$emailAccount) {
    echo "<p class='not-found'>No se encontro la cuenta de correo seleccionada (ID: $emailAccountId)</p>";
    exit;
}

echo "<p>Cuenta encontrada: " . $emailAccount->email . "</p>";

// Buscar plataforma
$platform = Platform::find($platformId);

if (!$platform) {
    echo "<p class='not-found'>Plataforma no encontrada</p>";
    exit;
}

echo "<p>Plataforma: " . $platform->name . "</p>";

// Obtener subjects de la plataforma
$subjects = $platform->subjects()->where('is_active', true)->pluck('subject')->toArray();

echo "<h3>Subjects configurados para esta plataforma:</h3>";
if (empty($subjects)) {
    echo "<p class='not-found'>No hay subjects configurados</p>";
} else {
    echo "<ul>";
    foreach ($subjects as $idx => $subj) {
        echo "<li class='platform-subject'>[" . ($idx+1) . "] \"" . htmlspecialchars($subj) . "\"</li>";
    }
    echo "</ul>";
}

try {
    $connector = new ImapConnector($emailAccount);
    $connector->connect();

    echo "<p>Conexion IMAP exitosa</p>";

    // Obtener emails recientes
    $searchTo = strtolower(trim($email));
    $recentEmails = $connector->getRecentEmails(72); // Ultimas 72 horas

    echo "<h2>Emails recientes (ultimas 72 horas): " . count($recentEmails) . " total</h2>";

    if (empty($recentEmails)) {
        echo "<p>No se encontraron emails recientes</p>";
    } else {
        // Ordenar por UID (mas reciente primero)
        rsort($recentEmails, SORT_NUMERIC);

        $foundEmail = null;
        $checkedCount = 0;

        foreach ($recentEmails as $uid) {
            $checkedCount++;
            $overview = @imap_fetch_overview($connector->getConnection(), $uid, FT_UID);
            if (!$overview) continue;

            $emailToRaw = $overview[0]->to ?? '(sin TO)';
            $emailSubjectRaw = $overview[0]->subject ?? '(sin subject)';
            $emailDate = $overview[0]->date ?? 'N/A';

            // Extraer email del TO
            $toAddress = $connector->extractEmailAddress($emailToRaw);

            echo "<div class='email " . ($toAddress && strtolower($toAddress) === $searchTo ? 'match' : 'no-match') . "'>";
            echo "<strong>UID $uid</strong> - $emailDate<br>";
            echo "TO (raw): " . htmlspecialchars($emailToRaw) . "<br>";
            echo "TO (extraido): <span class='to-email'>" . htmlspecialchars($toAddress ?? 'N/A') . "</span><br>";
            echo "Comparacion: '" . htmlspecialchars($toAddress ?? '') . "' === '" . htmlspecialchars($searchTo) . "' -> ";

            if ($toAddress && strtolower($toAddress) === $searchTo) {
                echo "<span style='color: #4ec9b0;'>COINCIDE</span><br>";

                // Verificar subject
                $subject = imap_utf8($emailSubjectRaw);
                echo "Subject (raw): " . htmlspecialchars($emailSubjectRaw) . "<br>";
                echo "Subject (decodificado): <span class='subject'>" . htmlspecialchars($subject) . "</span><br>";

                // Normalizar subject y patrones para comparacion
                $subjectNormalized = removeAccents($subject);

                $subjectMatch = false;
                foreach ($subjects as $platformSubject) {
                    $patternNormalized = removeAccents($platformSubject);
                    echo "  Comparando [\"$platformSubject\"] (normalizado: \"$patternNormalized\"): ";
                    $pos = stripos($subjectNormalized, $patternNormalized);
                    if ($pos !== false) {
                        echo "<span style='color: #4ec9b0;'>SI (posicion $pos)</span><br>";
                        $subjectMatch = true;
                        break;
                    } else {
                        // Mostrar diferencia
                        echo "<span style='color: #f14c4c;'>NO</span><br>";
                        echo "    <small>Subject normalizado: \"$subjectNormalized\"</small><br>";
                        echo "    <small>Patron normalizado: \"$patternNormalized\"</small><br>";
                    }
                }

                if ($subjectMatch) {
                    echo "<p class='found'>EMAIL ENCONTRADO!</p>";
                    $foundEmail = [
                        'uid' => $uid,
                        'to' => $toAddress,
                        'subject' => $subject,
                        'date' => $emailDate,
                    ];
                    break;
                } else {
                    echo "<p style='color: #ce9178;'>El TO coincide pero el subject NO coincide con ningun patron</p>";
                }
            } else {
                echo "<span style='color: #f14c4c;'>NO COINCIDE</span><br>";
                echo "Subject: " . htmlspecialchars(imap_utf8($emailSubjectRaw)) . "<br>";
            }
            echo "</div><hr>";
        }

        if (!$foundEmail) {
            echo "<h2 class='not-found'>No se encontro email que cumpla ambos criterios</h2>";
            echo "<p>Se revisaron $checkedCount emails.</p>";

            echo "<h3>Posibles causas:</h3>";
            echo "<ul>";
            echo "<li>El email no fue dirigido a este destinatario</li>";
            echo "<li>El subject del email no coincide con ningun patron configurado</li>";
            echo "<li>El email fue recibido hace mas de 72 horas</li>";
            echo "<li>La cuenta de correo consultada no es la correcta</li>";
            echo "</ul>";
        }
    }

    $connector->disconnect();

} catch (Exception $e) {
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
