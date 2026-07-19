<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = App\Models\Client::where('name', 'like', '%Luis%')->first();
if (!$c) $c = App\Models\Client::first();

$data = [
    'client_id' => $c->id,
    'access_mode' => $c->access_mode,
    'user_id' => $c->user_id
];

$data['selective_emails'] = $c->allowedEmails()->get()->toArray();

if ($c->user) {
    $data['all_emails'] = $c->user->allowedEmails()->get()->toArray();
}

file_put_contents('C:\Users\lapto\Desktop\debug.json', json_encode($data, JSON_PRETTY_PRINT));
echo "OK";
