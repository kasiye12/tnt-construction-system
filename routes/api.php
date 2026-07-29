<?php

use App\Http\Controllers\Api\V1\ProjectApiController;
use App\Http\Controllers\Api\V1\ReportApiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SyncController;
use App\Http\Controllers\Api\TelegramApiController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/telegram-login', [AuthController::class, 'telegramLogin']);
Route::post('/telegram/webhook', [TelegramApiController::class, 'webhook']);

// Protected routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Projects API - use different names
    Route::get('/projects', [ProjectApiController::class, 'index'])->name('api.projects.index');
    Route::post('/projects', [ProjectApiController::class, 'store'])->name('api.projects.store');
    Route::get('/projects/{id}', [ProjectApiController::class, 'show'])->name('api.projects.show');
    Route::put('/projects/{id}', [ProjectApiController::class, 'update'])->name('api.projects.update');
    Route::delete('/projects/{id}', [ProjectApiController::class, 'destroy'])->name('api.projects.destroy');
    
    // Reports API
    Route::get('/reports', [ReportApiController::class, 'index'])->name('api.reports.index');
    Route::post('/reports', [ReportApiController::class, 'store'])->name('api.reports.store');
    Route::get('/reports/{id}', [ReportApiController::class, 'show'])->name('api.reports.show');
    Route::get('/stats/today', [ReportApiController::class, 'todayStats'])->name('api.stats.today');
    
    // Sync routes
    Route::post('/sync/reports', [SyncController::class, 'syncReports']);
    Route::post('/sync/checkins', [SyncController::class, 'syncCheckins']);
    
    // User info
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('site')
        ]);
    });
});
