<?php

use App\Http\Controllers\Mobile\MobileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('mobile')->group(function () {
    Route::get('/', [MobileController::class, 'home']);
    Route::get('/reports', [MobileController::class, 'reports'])->name('mobile.reports');
    Route::get('/reports/create', [MobileController::class, 'createReport']);
    Route::post('/reports', [MobileController::class, 'storeReport']);
    Route::get('/checkin', [MobileController::class, 'checkin']);
    Route::post('/do-checkin', [MobileController::class, 'doCheckin']);
    Route::post('/do-checkout', [MobileController::class, 'doCheckout']);
    Route::get('/chat', [MobileController::class, 'chat']);
    Route::get('/profile', [MobileController::class, 'profile']);
});
