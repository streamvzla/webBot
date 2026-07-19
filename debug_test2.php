<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = App\Models\Client::find(2); // El cliente que está editando en la captura
if (!$c) {
    echo "No client found";
    exit;
}

$platformId = App\Models\Platform::where('name', 'like', '%Disney%')->first()->id ?? 1;

$req = Illuminate\Http\Request::create('/client/emails/by-platform', 'GET', ['platform_id' => $platformId]);
$app->make('auth')->guard('client')->setUser($c);
$ctrl = new App\Http\Controllers\Client\CodeQueryController();

try {
    $res = $ctrl->getEmailsByPlatform($req);
    echo $res->getContent();
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
