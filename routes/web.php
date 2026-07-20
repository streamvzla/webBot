<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Models\Client;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Install Routes
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [App\Http\Controllers\InstallController::class, 'welcome'])->name('step1');
    Route::get('/requirements', [App\Http\Controllers\InstallController::class, 'requirements'])->name('requirements');
    Route::get('/database', [App\Http\Controllers\InstallController::class, 'database'])->name('database');
    Route::post('/database', [App\Http\Controllers\InstallController::class, 'databasePost'])->name('database.post');
    Route::get('/admin', [App\Http\Controllers\InstallController::class, 'admin'])->name('admin');
    Route::post('/process', [App\Http\Controllers\InstallController::class, 'process'])->name('process');
    Route::get('/finish', [App\Http\Controllers\InstallController::class, 'finish'])->name('finish');
});

// Home - Redirigir al login
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// Cron Job Route - Reset query counts daily
Route::get('/cron', function () {
    $token = request('token');
    $expectedToken = env('CRON_TOKEN', 'your-secure-token-here');

    if ($token !== $expectedToken) {
        abort(403, 'Token inválido');
    }

    $resetCount = Client::where('query_count', '>', 0)->update(['query_count' => 0]);

    return response()->json([
        'success' => true,
        'message' => "Se han reseteado {$resetCount} contador(es) de consultas.",
        'timestamp' => now()->toIso8601String()
    ]);
})->name('cron.job');

// Public Query Routes (sin autenticación)
Route::prefix('query')->name('public.')->middleware([\App\Http\Middleware\CheckIpBan::class])->group(function () {
    Route::get('/', [App\Http\Controllers\Public\PublicQueryController::class, 'index'])->name('query');
    Route::get('/clear', [App\Http\Controllers\Public\PublicQueryController::class, 'clearSession'])->name('query.clear');
    Route::post('/', [App\Http\Controllers\Public\PublicQueryController::class, 'query'])
        ->name('query.submit')
        ->middleware('throttle:30,1'); // máx 10 búsquedas por minuto por IP
});

// Páginas de Información
Route::get('/guia', function () {
    return view('pages.guia');
})->name('guia');

Route::get('/acerca-de', function () {
    return view('pages.acerca');
})->name('acerca');

// Authentication Routes (handles both client and admin)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2FA Verification during login
Route::get('/2fa/verify', [App\Http\Controllers\TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
Route::post('/2fa/verify', [App\Http\Controllers\TwoFactorController::class, 'verifyLogin'])->name('2fa.verify.post');

// Crear usuario rápidamente desde login de admin — REQUIERE auth
// (Movido al grupo protegido abajo — ver Route::prefix('admin')...)

// Admin Routes (protected)
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\CheckFranchiseSubscription::class])->group(function () {
    // Subscription Expired View
    Route::get('/subscription-expired', function () {
        return view('admin.subscription-expired');
    })->name('admin.subscription.expired');

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update')->middleware([\App\Http\Middleware\CheckUserRole::class.':admin']);

    // Storage Link - Verificar y recrear link simbólico (solo admins)
    Route::post('/fix-storage-link', [App\Http\Controllers\Admin\DashboardController::class, 'fixStorageLink'])->name('admin.fix-storage-link');

    // Inventory (Mi Inventario para Revendedores y Admin)
    Route::get('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('admin.inventory.index');
    Route::post('/inventory/release', [App\Http\Controllers\Admin\InventoryController::class, 'release'])->name('admin.inventory.release');

    // Reseller Query Code (Consultar Código para Revendedores)
    Route::prefix('query')->name('admin.query.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ResellerQueryController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\ResellerQueryController::class, 'query'])->name('post');
        Route::get('/code', [App\Http\Controllers\Admin\ResellerQueryController::class, 'getTempCode'])->name('code');
        Route::get('/clear', [App\Http\Controllers\Admin\ResellerQueryController::class, 'clearSession'])->name('clear');
    });

    // ==========================================
    // SECCIÓN PROTEGIDA: SÓLO ADMIN / SUPER ADMIN
    // ==========================================
    Route::middleware([\App\Http\Middleware\CheckUserRole::class.':admin'])->group(function () {
        // Platforms
        Route::resource('platforms', App\Http\Controllers\Admin\PlatformController::class)->names([
            'index' => 'admin.platforms.index',
            'create' => 'admin.platforms.create',
            'store' => 'admin.platforms.store',
            'edit' => 'admin.platforms.edit',
            'update' => 'admin.platforms.update',
            'destroy' => 'admin.platforms.destroy',
        ]);

        // Platform Subjects
        Route::prefix('platforms/{platform}/subjects')->name('admin.platforms.subjects.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PlatformController::class, 'subjects'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\PlatformController::class, 'storeSubject'])->name('store');
            Route::delete('/{subject}', [App\Http\Controllers\Admin\PlatformController::class, 'destroySubject'])->name('destroy');
            Route::post('/{subject}/toggle-public', [App\Http\Controllers\Admin\PlatformController::class, 'toggleSubjectVisibility'])->name('togglePublic');
        });

        // Servers (Livewire 3)
        Route::get('/servers', \App\Livewire\Admin\ServerList::class)->name('admin.servers.index');
        Route::get('/servers/create', \App\Livewire\Admin\ServerForm::class)->name('admin.servers.create');
        Route::get('/servers/{server}/edit', \App\Livewire\Admin\ServerForm::class)->name('admin.servers.edit');

        // Email Accounts — Livewire 3
        Route::get('/email-accounts',               \App\Livewire\Admin\EmailAccountList::class)->name('admin.email-accounts.index');
        Route::get('/email-accounts/create',        \App\Livewire\Admin\EmailAccountForm::class)->name('admin.email-accounts.create');
        Route::get('/email-accounts/{account}/edit',\App\Livewire\Admin\EmailAccountForm::class)->name('admin.email-accounts.edit');

        // Test IMAP connection
        Route::post('/email-accounts/{emailAccount}/test-connection', [App\Http\Controllers\Admin\EmailAccountController::class, 'testConnection'])->name('admin.email-accounts.test-connection');
        Route::get('/email-accounts/{emailAccount}/test-connection-ajax', [App\Http\Controllers\Admin\EmailAccountController::class, 'testConnectionAjax'])->name('admin.email-accounts.test-connection-ajax');

        // Allowed Emails Mass Upload
        Route::get('/allowed-emails/mass-upload', [App\Http\Controllers\Admin\AllowedEmailController::class, 'massUpload'])->name('admin.allowed-emails.mass-upload');
        Route::post('/allowed-emails/mass-upload', [App\Http\Controllers\Admin\AllowedEmailController::class, 'massStore'])->name('admin.allowed-emails.mass-store');

        // Allowed Emails
        Route::resource('allowed-emails', App\Http\Controllers\Admin\AllowedEmailController::class)->names([
            'index' => 'admin.allowed-emails.index',
            'create' => 'admin.allowed-emails.create',
            'store' => 'admin.allowed-emails.store',
            'edit' => 'admin.allowed-emails.edit',
            'update' => 'admin.allowed-emails.update',
            'destroy' => 'admin.allowed-emails.destroy',
        ]);
        
    });

    // Queries
    Route::resource('queries', App\Http\Controllers\Admin\QueryController::class)->names([
        'index' => 'admin.queries.index',
        'show' => 'admin.queries.show',
        'destroy' => 'admin.queries.destroy',
    ]);

    // Truncate all queries
    Route::post('/queries/truncate', [App\Http\Controllers\Admin\QueryController::class, 'truncate'])->name('admin.queries.truncate');

    // Profile (Accessible by both Admin and User/Franchise)
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');

    // Users (Mi Equipo / Franquicias & Staff)
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->names([
        'index'   => 'admin.users.index',
        'create'  => 'admin.users.create',
        'store'   => 'admin.users.store',
        'edit'    => 'admin.users.edit',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::post('/users/{user}/renew', [App\Http\Controllers\Admin\UserController::class, 'renew'])->name('admin.users.renew');

    // Crear usuario rápido (dentro del grupo auth protegido)
    Route::post('/users/quick-create', [App\Http\Controllers\Admin\UserController::class, 'createQuick'])->name('admin.users.quick-create');

    // Rutas exclusivas para el Súper Administrador / Admin
    Route::middleware([\App\Http\Middleware\CheckUserRole::class.':admin'])->group(function () {
        // --- SUPER ADMIN ONLY (ID = 1) ---
        Route::middleware([\App\Http\Middleware\CheckSuperAdmin::class])->group(function () {
            // Planes de Franquicia (Exclusivo Súper Admin)
            Route::resource('franchise-plans', App\Http\Controllers\Admin\FranchisePlanController::class)->names([
                'index' => 'admin.franchise-plans.index',
                'create' => 'admin.franchise-plans.create',
                'store' => 'admin.franchise-plans.store',
                'edit' => 'admin.franchise-plans.edit',
                'update' => 'admin.franchise-plans.update',
                'destroy' => 'admin.franchise-plans.destroy',
            ]);

            Route::get('/licenses', \App\Livewire\Admin\LicenseManager::class)->name('admin.licenses.index');
            Route::get('/licenses/create', \App\Livewire\Admin\LicenseForm::class)->name('admin.licenses.create');
            Route::get('/licenses/{license}/edit', \App\Livewire\Admin\LicenseForm::class)->name('admin.licenses.edit');
        });
        
        // IP Bans (Anti-Spam)
        Route::get('/ip-bans', [App\Http\Controllers\Admin\IpBanController::class, 'index'])->name('admin.ip-bans.index');
        Route::delete('/ip-bans/{ipBan}', [App\Http\Controllers\Admin\IpBanController::class, 'destroy'])->name('admin.ip-bans.destroy');
        // ---------------------------------
    });

    // Clients
    Route::resource('clients', App\Http\Controllers\Admin\ClientController::class)->names([
        'index' => 'admin.clients.index',
        'create' => 'admin.clients.create',
        'store' => 'admin.clients.store',
        'edit' => 'admin.clients.edit',
        'update' => 'admin.clients.update',
        'destroy' => 'admin.clients.destroy',
    ]);
    
    // Warranties
    Route::resource('warranties', App\Http\Controllers\Admin\WarrantyController::class)->only(['index', 'update', 'show', 'store'])->names([
        'index' => 'admin.warranties.index',
        'update' => 'admin.warranties.update',
        'show' => 'admin.warranties.show',
        'store' => 'admin.warranties.store',
    ]);

    // Reset client query count
    Route::post('/clients/{client}/reset-queries', [App\Http\Controllers\Admin\ClientController::class, 'resetQueryCount'])->name('admin.clients.reset-queries');

    // Activate/Deactivate client
    Route::post('/clients/{client}/activate', [App\Http\Controllers\Admin\ClientController::class, 'activate'])->name('admin.clients.activate');
    Route::post('/clients/{client}/deactivate', [App\Http\Controllers\Admin\ClientController::class, 'deactivate'])->name('admin.clients.deactivate');


    // 2FA Routes (Admin/User)
    Route::post('/2fa/enable', [App\Http\Controllers\TwoFactorController::class, 'enable'])->name('admin.2fa.enable');
    Route::post('/2fa/confirm', [App\Http\Controllers\TwoFactorController::class, 'confirm'])->name('admin.2fa.confirm');
    Route::post('/2fa/disable', [App\Http\Controllers\TwoFactorController::class, 'disable'])->name('admin.2fa.disable');

    // API Keys Management
    Route::post('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'generate'])->name('admin.api-keys.generate');
    Route::delete('/api-keys/{token}', [App\Http\Controllers\Admin\ApiKeyController::class, 'revoke'])->name('admin.api-keys.revoke');
});

// Client Routes
Route::prefix('client')->name('client.')->group(function () {
    // Logout route (uses unified AuthController)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Email change verification (no auth required — user may click from any device)
    Route::get('/email-change/verify/{token}', [App\Http\Controllers\Client\ClientAuthController::class, 'confirmEmailChange'])->name('email-change.verify');

    // Protected Client Routes
    Route::middleware('auth:client')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Client\ClientAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\Client\ClientAuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Client\ClientAuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/avatar', [App\Http\Controllers\Client\ClientAuthController::class, 'uploadAvatar'])->name('profile.avatar');
        Route::post('/profile/request-email-change', [App\Http\Controllers\Client\ClientAuthController::class, 'requestEmailChange'])->name('profile.request-email-change');
        Route::get('/query', [App\Http\Controllers\Client\CodeQueryController::class, 'index'])->name('query');
        Route::post('/query', [App\Http\Controllers\Client\CodeQueryController::class, 'query'])->name('query.post')->middleware('throttle:30,1');
        Route::get('/query/clear', [App\Http\Controllers\Client\CodeQueryController::class, 'clearSession'])->name('query.clear');
        Route::get('/guide', function () { return view('client.guide'); })->name('guide');
        Route::get('/about', function () { return view('client.about'); })->name('about');
        Route::get('/query/code', [App\Http\Controllers\Client\CodeQueryController::class, 'getTempCode'])->name('query.code');
        Route::get('/query/limit', [App\Http\Controllers\Client\ClientAuthController::class, 'getLimitStatus'])->name('query.limit');


    // Warranties
    // API Keys (comentado porque el controlador no existe)
    // Route::get('/api-keys', [App\Http\Controllers\Client\ApiKeyController::class, 'index'])->name('client.api-keys.index');
    // Route::post('/api-keys', [App\Http\Controllers\Client\ApiKeyController::class, 'store'])->name('client.api-keys.store');
    // Route::delete('/api-keys/{id}', [App\Http\Controllers\Client\ApiKeyController::class, 'destroy'])->name('client.api-keys.destroy');

    Route::post('/2fa/enable',   [App\Http\Controllers\TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/2fa/confirm',  [App\Http\Controllers\TwoFactorController::class, 'confirm'])->name('2fa.confirm');
        Route::post('/2fa/disable',  [App\Http\Controllers\TwoFactorController::class, 'disable'])->name('2fa.disable');

        // IMAP Diagnostic routes (requieren auth:client)
        Route::post('/diagnose/imap',       [App\Http\Controllers\Client\CodeQueryController::class, 'diagnoseImap'])->name('diagnose.imap');
        Route::get('/emails/recent',        [App\Http\Controllers\Client\CodeQueryController::class, 'listRecentEmails'])->name('emails.recent');
        Route::get('/emails/match-subjects',[App\Http\Controllers\Client\CodeQueryController::class, 'matchSubjects'])->name('emails.match-subjects');
        Route::get('/emails/by-platform',   [App\Http\Controllers\Client\CodeQueryController::class, 'getEmailsByPlatform'])->name('emails.by-platform');

        // Garantías del cliente
        Route::resource('warranties', App\Http\Controllers\Client\WarrantyController::class)->names([
            'index'   => 'warranties.index',
            'create'  => 'warranties.create',
            'store'   => 'warranties.store',
            'show'    => 'warranties.show',
            'edit'    => 'warranties.edit',
            'update'  => 'warranties.update',
            'destroy' => 'warranties.destroy',
        ]);
    });
});
