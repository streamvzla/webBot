import os
import re

src_dir = r'C:\Users\lapto\Desktop\botcodigo\tu-codigo_super_admin\resources\views\livewire\admin'
out_dir = r'C:\Users\lapto\Desktop\botcodigo\tu-codigo_super_admin\app\Livewire\Admin'
os.makedirs(out_dir, exist_ok=True)

files = ['dashboard-metrics.blade.php', 'allowed-email-list.blade.php', 'client-list.blade.php', 'global-search.blade.php']
class_names = ['DashboardMetrics', 'AllowedEmailList', 'ClientList', 'GlobalSearch']

for f, c in zip(files, class_names):
    path = os.path.join(src_dir, f)
    if os.path.exists(path):
        with open(path, 'r', encoding='utf-8') as file:
            content = file.read()
        
        match = re.search(r'<\?php\s+(.*?)new class extends Component\s*{(.*?)^};\s*\?>\s*(.*)', content, re.DOTALL | re.MULTILINE)
        if match:
            imports = match.group(1).replace('use Livewire\\Volt\\Component;', 'use Livewire\Component;')
            body = match.group(2)
            blade = match.group(3)
            
            php_code = f"<?php\n\nnamespace App\Livewire\Admin;\n\n{imports}class {c} extends Component\n{{\n{body}    public function render()\n    {{\n        return view('livewire.admin.{f.replace('.blade.php', '')}');\n    }}\n}}\n"
            
            with open(os.path.join(out_dir, f'{c}.php'), 'w', encoding='utf-8') as pyf:
                pyf.write(php_code)
                
            with open(path, 'w', encoding='utf-8') as bf:
                bf.write(blade.lstrip())
                
            print(f'Converted {f} to {c}.php')
        else:
            print(f'No match for {f}')
    else:
        print(f'File not found: {f}')
