<?php
// Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Verificar configuracion de settings ===\n\n";

$setting = Setting::first();

if (!$setting) {
    echo "ERROR: No hay settings en la base de datos\n";
    exit(1);
}

echo "Settings encontrados (ID: {$setting->id}):\n\n";

echo "query_cooldown_minutes = " . ($setting->query_cooldown_minutes ?? 'NULL') . "\n";
echo "query_limit_minutes = " . ($setting->query_limit_minutes ?? 'NULL') . "\n\n";

echo "Valor que deveria usar el sistema:\n";
$minutes = $setting->{Setting::KEY_QUERY_COOLDOWN_MINUTES} ?? 30;
echo "query_cooldown_minutes (KEY_QUERY_COOLDOWN_MINUTES) = {$minutes}\n";
