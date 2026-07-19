<?php

namespace App\Http\Controllers\Admin;

use App\Models\Platform;
use App\Models\Query;
use App\Models\User;
use App\Models\AllowedEmail;
use App\Models\Setting;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

class DashboardController
{
    public function __construct()
    {
        View::share('settings', [
            'email_filter_enabled' => Setting::get(Setting::KEY_EMAIL_FILTER_ENABLED, false),
            'query_cooldown_minutes' => Setting::get(Setting::KEY_QUERY_COOLDOWN_MINUTES, 30),
            'seo_title' => Setting::get(Setting::KEY_SEO_TITLE, 'Code Verification System'),
            'seo_description' => Setting::get(Setting::KEY_SEO_DESCRIPTION, 'Get your verification codes instantly'),
            'site_logo' => Setting::get(Setting::KEY_SITE_LOGO, ''),
            'web_url' => Setting::get(Setting::KEY_WEB_URL, ''),
            'telegram_url' => Setting::get(Setting::KEY_TELEGRAM_URL, ''),
            'whatsapp_url' => Setting::get(Setting::KEY_WHATSAPP_URL, ''),
            'whatsapp_message' => Setting::get(Setting::KEY_WHATSAPP_MESSAGE, 'Hello, I need help'),
            'vendor_id' => Setting::get(Setting::KEY_VENDOR_ID, ''),
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $isRegularUser = $user && $user->role === 'user';

        // Verificar estado del link simbólico de storage
        $storageLinkStatus = $this->checkStorageLink();

        // Base query builder para consultas filtradas por usuario
        $userQuery = $isRegularUser ? Query::where('user_id', $user->id) : Query::query();
        $userPlatforms = $isRegularUser ? Platform::where('user_id', $user->id) : Platform::query();
        $userClients = $isRegularUser ? Client::where('user_id', $user->id) : Client::query();
        $userAllowedEmails = $isRegularUser ? AllowedEmail::where('user_id', $user->id) : AllowedEmail::query();

        $stats = [
            'total_queries'       => (clone $userQuery)->count(),
            'queries_today'       => (clone $userQuery)->whereDate('created_at', today())->count(),
            'queries_this_week'   => (clone $userQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_users'         => $isRegularUser ? 0 : User::count(),
            'total_platforms'     => (clone $userPlatforms)->count(),
            'total_allowed_emails'=> (clone $userAllowedEmails)->count(),
            'total_clients'       => (clone $userClients)->count(),
            'recent_queries'      => (clone $userQuery)->with(['user', 'platform', 'client'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'queries_by_platform' => (clone $userQuery)->selectRaw('platform_id, COUNT(*) as count')
                ->groupBy('platform_id')
                ->pluck('count', 'platform_id')
                ->toArray(),
            'is_regular_user'  => $isRegularUser,
            'storage_link_ok'  => $storageLinkStatus['link_working'],
            'is_admin'         => $user && $user->role === 'admin',
            'storage_status'   => $storageLinkStatus,

            // Cuentas de correo: libres, ocupadas y con vencimientos
            'emails_free'      => (clone $userAllowedEmails)->whereDoesntHave('clients', function ($q) {
                                      $q->where(function ($c) {
                                          $c->whereNull('allowed_email_client.expires_at')
                                            ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                                      });
                                  })->count(),
            'emails_occupied'  => (clone $userAllowedEmails)->whereHas('clients', function ($q) {
                                      $q->where(function ($c) {
                                          $c->whereNull('allowed_email_client.expires_at')
                                            ->orWhereDate('allowed_email_client.expires_at', '>=', now()->toDateString());
                                      });
                                  })->count(),
            'emails_expired'   => (clone $userAllowedEmails)->whereHas('clients', function ($q) {
                                      $q->whereNotNull('allowed_email_client.expires_at')
                                        ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                                  })->count(),

            // Nivel SaaS: Alertas de Renovación
            'clients_expiring_soon' => (clone $userClients)->whereHas('allowedEmails', function ($q) {
                                      $q->whereNotNull('allowed_email_client.expires_at')
                                        ->whereDate('allowed_email_client.expires_at', '>=', now()->toDateString())
                                        ->whereDate('allowed_email_client.expires_at', '<=', now()->addDays(7)->toDateString());
                                  })->with(['allowedEmails' => function($q) {
                                      $q->whereNotNull('allowed_email_client.expires_at')
                                        ->orderBy('allowed_email_client.expires_at', 'asc');
                                  }])->get(),
                                  
            'clients_expired'      => (clone $userClients)->whereHas('allowedEmails', function ($q) {
                                      $q->whereNotNull('allowed_email_client.expires_at')
                                        ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                                  })->with(['allowedEmails' => function($q) {
                                      $q->whereNotNull('allowed_email_client.expires_at')
                                        ->whereDate('allowed_email_client.expires_at', '<', now()->toDateString());
                                  }])->get(),
            // Plan del franquiciado (solo si es role=user)
            'plan'             => $isRegularUser ? $user->franchisePlan : null,
            'plan_clients_used'=> $isRegularUser ? (clone $userClients)->count() : null,
            // Anti-Spam Security Center
            'security_threats' => \App\Models\IpBan::with('client')->orderBy('created_at', 'desc')->limit(5)->get(),
            // Franquicias por vencer (Solo Super Admin)
            'franchises_expiring' => auth()->id() === 1 ? User::whereNotNull('subscription_ends_at')
                                        ->where('role', 'admin')
                                        ->whereDate('subscription_ends_at', '<=', now()->addDays(7))
                                        ->orderBy('subscription_ends_at', 'asc')
                                        ->get() : collect(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Verificar si el link simbólico de storage/public existe y funciona correctamente
     */
    private function checkStorageLink(): array
    {
        $storagePath = public_path('storage');
        $targetPath = storage_path('app/public');

        $isSymlink = is_link($storagePath);
        $isDir = is_dir($storagePath);
        $targetExists = is_dir($targetPath);

        // Verificar si el link funciona correctamente
        $linkWorking = false;
        if ($isSymlink && $targetExists) {
            // Es un symlink y el target existe
            $linkWorking = true;
        } elseif ($isSymlink && !$targetExists) {
            // Es un symlink pero el target no existe (roto)
            $linkWorking = false;
        } elseif ($isDir && !$isSymlink && $targetExists) {
            // Es un directorio regular (no symlink), verificar si tiene contenido correcto
            // Comparar el número de archivos
            $storageContents = count(glob($storagePath . '/*'));
            $targetContents = count(glob($targetPath . '/*'));
            $linkWorking = ($storageContents > 0 && $targetContents > 0);
        }

        // Detectar si hay un directorio huérfano (symlink roto que dejó directorio)
        $isOrphaned = $isDir && !$isSymlink && !$linkWorking && $targetExists;

        return [
            'exists' => $isSymlink || $isDir,
            'is_symlink' => $isSymlink,
            'is_broken_link' => $isSymlink && !$targetExists,
            'is_orphaned_directory' => $isOrphaned,
            'link_working' => $linkWorking,
            'target_exists' => $targetExists,
            'path' => $storagePath,
            'target' => $targetPath,
        ];
    }

    /**
     * Recrear el link simbólico de storage (solo para administradores)
     */
    public function fixStorageLink(Request $request)
    {
        $user = Auth::user();

        // Verificar que sea administrador
        if (!$user || $user->role !== 'admin') {
            return Redirect::back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $storagePath = public_path('storage');
        $targetPath = storage_path('app/public');
        $status = $this->checkStorageLink();

        // Verificar que el directorio target exista
        if (!is_dir($targetPath)) {
            return Redirect::back()->with('error', 'Error: El directorio target no existe. Ejecuta: php artisan storage:link');
        }

        try {
            // Casos diferentes según el estado actual:

            // 1. Es un symlink roto
            if ($status['is_symlink'] && !$status['target_exists']) {
                unlink($storagePath);
            }
            // 2. Es un directorio huérfano (symlink roto que dejó directorio)
            elseif ($status['is_orphaned_directory']) {
                File::deleteDirectory($storagePath);
            }
            // 3. Es un directorio regular (no symlink)
            elseif ($status['exists'] && !$status['is_symlink']) {
                // Renombrar el directorio actual como backup
                $backupPath = public_path('storage_backup_' . time());
                rename($storagePath, $backupPath);
            }

            // Crear el link simbólico
            if (PHP_OS === 'WINNT') {
                // En Windows, intentar primero con symlink normal
                $success = @symlink($targetPath, $storagePath);

                if (!$success) {
                    // Si falla, intentar con junction point
                    $success = $this->createWindowsJunction($targetPath, $storagePath);
                }

                // Si todo falla, intentar eliminar y crear de nuevo
                if (!$success && is_dir($storagePath)) {
                    File::deleteDirectory($storagePath);
                    $success = @symlink($targetPath, $storagePath);
                    if (!$success) {
                        $success = $this->createWindowsJunction($targetPath, $storagePath);
                    }
                }
            } else {
                $success = symlink($targetPath, $storagePath);
            }

            if ($success) {
                // Verificar que funcione
                if (is_link($storagePath) || is_dir($storagePath)) {
                    return Redirect::back()->with('success', 'Link simbólico de storage recreado exitosamente.');
                }
            }

            // Si llegamos aquí, no se pudo crear el symlink automáticamente
            return Redirect::back()->with('error', "No se pudo crear el link simbólico automáticamente.\n\n" .
                "Por favor, ejecuta estos comandos en tu servidor via SSH:\n" .
                "1. rm -rf " . $storagePath . " (o elimina la carpeta storage manualmente)\n" .
                "2. php artisan storage:link\n\n" .
                "O en Windows (como Administrador):\n" .
                "1. rmdir " . $storagePath . "\n" .
                "2. mklink /J " . $storagePath . " " . $targetPath);

        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Error al recrear el link: ' . $e->getMessage() . "\n\n" .
                "Ejecuta en SSH: php artisan storage:link");
        }
    }

    /**
     * Crear junction point en Windows (fallback para symlink)
     */
    private function createWindowsJunction(string $target, string $junction): bool
    {
        // Verificar si es Windows
        if (PHP_OS !== 'WINNT') {
            return false;
        }

        // Si el junction ya existe, eliminarlo
        if (is_dir($junction)) {
            rmdir($junction);
        }

        // Usar el comando mklink de Windows
        $command = "mklink /J \"" . escapeshellarg($junction) . "\" \"" . escapeshellarg($target) . "\"";
        exec($command, $output, $returnVar);

        return $returnVar === 0;
    }

    public function settings()
    {
        $settings = [
            'seo_title' => Setting::get(Setting::KEY_SEO_TITLE, 'Code Verification System'),
            'seo_description' => Setting::get(Setting::KEY_SEO_DESCRIPTION, ''),
            'vendor_id' => Setting::get(Setting::KEY_VENDOR_ID, ''),
            'email_filter_enabled' => Setting::get(Setting::KEY_EMAIL_FILTER_ENABLED, false),
            'query_cooldown_minutes' => Setting::get(Setting::KEY_QUERY_COOLDOWN_MINUTES, 30),
            'web_url' => Setting::get(Setting::KEY_WEB_URL, ''),
            'telegram_url' => Setting::get(Setting::KEY_TELEGRAM_URL, ''),
            'whatsapp_url' => Setting::get(Setting::KEY_WHATSAPP_URL, ''),
            'whatsapp_message' => Setting::get(Setting::KEY_WHATSAPP_MESSAGE, ''),
        ];

        return view('admin.settings', compact('settings'));
    }
}
