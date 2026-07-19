<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

Auth::login(App\Models\User::first());
$c = Livewire\Livewire::test(App\Livewire\Admin\PlatformList::class);
$p = App\Models\Platform::first();
if ($p) {
    try {
        $c->call('duplicatePlatform', $p->id);
        echo 'DUPLICATE SUCCESS';
    } catch (\Exception $e) {
        echo 'ERROR DUPLICATING: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
    }
} else {
    echo 'No platforms found.';
}
