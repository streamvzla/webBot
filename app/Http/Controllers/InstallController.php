<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use PDO;

class InstallController extends Controller
{
    public function welcome()
    {
        return view('install.step1_welcome');
    }

    public function requirements()
    {
        $requirements = [
            'php' => version_compare(PHP_VERSION, '8.3.0', '>='),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'curl' => extension_loaded('curl'),
            'xml' => extension_loaded('xml'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
            'bcmath' => extension_loaded('bcmath'),
            'fileinfo' => extension_loaded('fileinfo'),
            'imap' => extension_loaded('imap'),
        ];

        $permissions = [
            'storage' => is_writable(storage_path()),
            'bootstrap' => is_writable(base_path('bootstrap/cache')),
            'env' => is_writable(base_path()) || is_writable(base_path('.env')),
        ];

        $allRequirementsMet = !in_array(false, $requirements) && !in_array(false, $permissions);

        return view('install.step2_requirements', compact('requirements', 'permissions', 'allRequirementsMet'));
    }

    public function database()
    {
        return view('install.step3_database');
    }

    public function databasePost(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required',
            'db_database' => 'required',
            'db_username' => 'required',
        ]);

        try {
            $dsn = "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}";
            $pdo = new PDO($dsn, $request->db_username, $request->db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            
            // Guardar configuración temporal en sesión para usarla en el siguiente paso
            session([
                'install_db_host' => $request->db_host,
                'install_db_port' => $request->db_port,
                'install_db_database' => $request->db_database,
                'install_db_username' => $request->db_username,
                'install_db_password' => $request->db_password,
            ]);

            return redirect()->route('install.admin')->with('success', 'Datos de la base de datos conectados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión: ' . $e->getMessage())->withInput();
        }
    }

    public function admin()
    {
        if (!session()->has('install_db_host')) {
            return redirect()->route('install.database');
        }
        return view('install.step4_admin');
    }

    public function process(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        if (!session()->has('install_db_host')) {
            return redirect()->route('install.database');
        }

        try {
            // 1. Crear el archivo .env
            $this->createEnvFile();

            // 2. Ejecutar Migraciones
            // Como el entorno acaba de ser modificado, necesitamos limpiar la config
            Artisan::call('config:clear');
            
            // Forzar configuración dinámica para la base de datos en esta petición
            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => session('install_db_host'),
                'database.connections.mysql.port' => session('install_db_port'),
                'database.connections.mysql.database' => session('install_db_database'),
                'database.connections.mysql.username' => session('install_db_username'),
                'database.connections.mysql.password' => session('install_db_password'),
            ]);

            DB::purge('mysql');

            Artisan::call('migrate', ['--force' => true]);

            // 3. Crear Admin
            DB::table('users')->insert([
                'name' => $request->admin_name,
                'username' => explode('@', $request->admin_email)[0],
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Crear Setting inicial
            DB::table('settings')->insert([
                ['key' => 'site_name', 'value' => 'NexusCode'],
                ['key' => 'seo_title', 'value' => 'NexusCode - Panel de Códigos'],
            ]);

            // 5. Crear archivo de bloqueo
            File::put(storage_path('installed.lock'), 'Instalado el ' . now());

            // Limpiar sesión
            session()->flush();

            return redirect()->route('install.finish');

        } catch (\Exception $e) {
            return back()->with('error', 'Error durante la instalación: ' . $e->getMessage())->withInput();
        }
    }

    public function finish()
    {
        if (!File::exists(storage_path('installed.lock'))) {
            return redirect()->route('install.step1');
        }
        return view('install.step5_finish');
    }

    private function createEnvFile()
    {
        $envExamplePath = base_path('.env.example');
        $envPath = base_path('.env');
        
        $envContent = "";
        
        if (File::exists($envExamplePath)) {
            $envContent = File::get($envExamplePath);
        } else {
            $envContent = "APP_NAME=NexusCode\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=" . url('/') . "\n\n";
        }

        // Reemplazar base de datos
        $envContent = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=mysql', $envContent);
        $envContent = preg_replace('/DB_HOST=.*/', 'DB_HOST=' . session('install_db_host'), $envContent);
        $envContent = preg_replace('/DB_PORT=.*/', 'DB_PORT=' . session('install_db_port'), $envContent);
        $envContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . session('install_db_database'), $envContent);
        $envContent = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . session('install_db_username'), $envContent);
        $envContent = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . session('install_db_password'), $envContent);
        
        // Ensure some defaults for production
        $envContent = preg_replace('/APP_ENV=.*/', 'APP_ENV=production', $envContent);
        $envContent = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG=false', $envContent);

        File::put($envPath, $envContent);
        
        // Generate app key
        Artisan::call('key:generate', ['--force' => true]);
    }
}



