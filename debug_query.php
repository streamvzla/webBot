<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $c = App\Models\Client::find(2); // Sabemos que es el ID 2 por la captura
    if (!$c) {
        echo "CLIENT 2 NOT FOUND\n";
        $c = App\Models\Client::first();
        echo "Using first client: ID " . ($c ? $c->id : 'none') . "\n";
    }

    if ($c) {
        echo "Client ID: " . $c->id . "\n";
        echo "Access Mode: " . $c->access_mode . "\n";
        
        $pivot = \DB::table('allowed_email_client')->where('client_id', $c->id)->get();
        echo "Pivot entries:\n";
        foreach ($pivot as $p) {
            echo "- Email ID: " . $p->allowed_email_id . " | Expires: " . $p->expires_at . "\n";
        }
        
        $emails = $c->allowedEmails()->get();
        echo "Emails from relationship: " . $emails->count() . "\n";
        
        foreach ($emails as $email) {
            echo "- ID: " . $email->id . " | Active: " . $email->is_active . " | Paused: " . $email->paused_at . " | Platform ID: " . $email->platform_id . "\n";
        }
        
        $platformId = App\Models\Platform::where('name', 'like', '%Disney%')->first()->id ?? null;
        echo "Platform ID for Disney: " . $platformId . "\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
