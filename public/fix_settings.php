<?php
// Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Verificar y configurar settings ===\n\n";

// Verificar query_cooldown_minutes
$value = Setting::get('query_cooldown_minutes');
echo "query_cooldown_minutes actual: " . ($value ?? 'NO DEFINIDO') . "\n";

// Configurar a 0 para pruebas
Setting::set('query_cooldown_minutes', 0, Setting::TYPE_INTEGER);
echo "query_cooldown_minutes establecido a: " . Setting::get('query_cooldown_minutes') . "\n\n";

// Verificar otros valores
echo "Valor de email_filter_enabled: " . (Setting::get('email_filter_enabled') ? 'true' : 'false') . "\n\n";

echo "✅ Configuracion actualizada.\n";
echo "Ahora puedes hacer consultas sin limite de tiempo.\n";
