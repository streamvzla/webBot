<?php

$src_dir = __DIR__ . '/resources/views/livewire/admin';
$out_dir = __DIR__ . '/app/Livewire/Admin';

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0777, true);
}

$files = ['dashboard-metrics.blade.php', 'allowed-email-list.blade.php', 'client-list.blade.php', 'global-search.blade.php'];
$class_names = ['DashboardMetrics', 'AllowedEmailList', 'ClientList', 'GlobalSearch'];

foreach ($files as $i => $f) {
    $c = $class_names[$i];
    $path = $src_dir . '/' . $f;
    
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        if (preg_match('/<\?php\s+(.*?)new class extends Component\s*(?:\{|\n\{)(.*?)^\s*};\s*\?>\s*(.*)/ms', $content, $matches)) {
            $imports = str_replace('use Livewire\\Volt\\Component;', 'use Livewire\Component;', $matches[1]);
            $body = $matches[2];
            $blade = ltrim($matches[3]);
            
            $bladeName = str_replace('.blade.php', '', $f);
            $php_code = "<?php\n\nnamespace App\Livewire\Admin;\n\n{$imports}class {$c} extends Component\n{\n{$body}    public function render()\n    {\n        return view('livewire.admin.{$bladeName}');\n    }\n}\n";
            
            file_put_contents($out_dir . '/' . $c . '.php', $php_code);
            file_put_contents($path, $blade);
            
            echo "Converted {$f} to {$c}.php\n";
        } else {
            echo "No match for {$f}\n";
        }
    } else {
        echo "File not found: {$f}\n";
    }
}
