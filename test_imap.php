<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$account = \App\Models\EmailAccount::where('email', 'cuentas@streamvzla.com')->first();
$password = \Illuminate\Support\Facades\Crypt::decryptString($account->password);

$clientManager = new \Webklex\PHPIMAP\ClientManager();
$client = $clientManager->make([
    'host'          => $account->host,
    'port'          => $account->port,
    'encryption'    => $account->encryption,
    'validate_cert' => false,
    'username'      => $account->email,
    'password'      => $password,
    'protocol'      => 'imap'
]);
$client->connect();
$folder = $client->getFolder('INBOX');
echo "Connected to: " . $account->email . "\n";
$messages = $folder->query()->unseen()->get();
echo "Unseen count (all): " . $messages->count() . "\n";

$messagesDesc = $folder->query()->unseen()->setFetchOrderDesc()->limit(50, 1)->get();
echo "Unseen count (desc limit): " . $messagesDesc->count() . "\n";
