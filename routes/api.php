<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiQueryController;
use App\Http\Middleware\CheckApiKeyUserActive;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', CheckApiKeyUserActive::class])->prefix('v1')->group(function () {
    // Perfil y límites
    Route::get('/profile', [ApiQueryController::class, 'getProfile']);
    
    // Plataformas y Asuntos
    Route::get('/platforms', [ApiQueryController::class, 'getPlatforms']);
    
    // Correos asignados/disponibles
    Route::get('/emails', [ApiQueryController::class, 'getEmails']);
    Route::get('/emails/recent', [ApiQueryController::class, 'getRecentEmails']);
    
    // Consultar código
    Route::post('/query', [ApiQueryController::class, 'query']);
});

// --- CODEBOT LICENSE VALIDATION API (PUBLIC) ---
Route::prefix('v1/license')->group(function () {
    Route::post('/validate', [\App\Http\Controllers\Api\LicenseController::class, 'validateLicense']);
    Route::post('/heartbeat', [\App\Http\Controllers\Api\LicenseController::class, 'heartbeat']);
});

// --- CODEBOT OTA UPDATES API (PUBLIC) ---
Route::prefix('v1/updates')->group(function () {
    Route::get('/check', [\App\Http\Controllers\Api\UpdateController::class, 'check']);
    Route::post('/download', [\App\Http\Controllers\Api\UpdateController::class, 'download']);
});
// -----------------------------------------------
