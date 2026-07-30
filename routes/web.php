<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SafetyController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth routes
require __DIR__.'/auth.php';

// Telegram auth
Route::get('/auth/telegram', function () {
    return view('auth.telegram-login');
})->name('telegram.login');

// Protected routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    
    // Sites
    Route::resource('sites', SiteController::class);
    
    // Daily Reports
    Route::get('/reports/export', [ReportExportController::class, 'index'])->name('reports.export');
    Route::get('/reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportExportController::class, 'exportPDF'])->name('reports.export.pdf');
    Route::post('/reports/{report}/approve', [DailyReportController::class, 'approve'])->name('reports.approve');
    Route::post('/reports/{report}/reject', [DailyReportController::class, 'reject'])->name('reports.reject');
    Route::resource('reports', DailyReportController::class);
    
    // Users
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);
    
    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{channel}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{channel}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/direct', [ChatController::class, 'startDirectChat'])->name('chat.direct');
    Route::post('/chat/create', [ChatController::class, 'createChannel'])->name('chat.create');
    Route::delete('/chat/message/{message}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
    
    // Equipment
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    
    // Materials
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    
    // Safety
    Route::resource('safety', SafetyController::class);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    
    // Mobile
    Route::get('/mobile', [App\Http\Controllers\Mobile\MobileController::class, 'home'])->name('mobile.home');
    Route::get('/mobile/reports', [App\Http\Controllers\Mobile\MobileController::class, 'reports'])->name('mobile.reports');
    Route::get('/mobile/reports/create', [App\Http\Controllers\Mobile\MobileController::class, 'createReport']);
    Route::post('/mobile/reports', [App\Http\Controllers\Mobile\MobileController::class, 'storeReport']);
    Route::get('/mobile/checkin', [App\Http\Controllers\Mobile\MobileController::class, 'checkin']);
    Route::post('/mobile/checkin', [App\Http\Controllers\Mobile\MobileController::class, 'doCheckin']);
    Route::post('/mobile/checkout', [App\Http\Controllers\Mobile\MobileController::class, 'doCheckout']);
    Route::get('/mobile/profile', [App\Http\Controllers\Mobile\MobileController::class, 'profile']);
});

// Mobile Chat
Route::get('/mobile/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('mobile.chat');
Route::get('/mobile/chat/{channel}', [App\Http\Controllers\ChatController::class, 'show'])->name('mobile.chat.show');

// Report print route
Route::get('/reports/{report}/print', [App\Http\Controllers\ReportExportController::class, 'printReport'])->name('reports.print');

// Equipment routes
Route::resource('equipment', App\Http\Controllers\EquipmentController::class);

// Materials routes
Route::resource('materials', App\Http\Controllers\MaterialController::class);

// Safety routes
Route::resource('safety', App\Http\Controllers\SafetyController::class);
Route::post('safety/{safety}/resolve', [App\Http\Controllers\SafetyController::class, 'resolve'])->name('safety.resolve');
Route::get('/notifications/latest', [App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.latest');

// Chat typing route
Route::post('/chat/{channel}/typing', [App\Http\Controllers\ChatController::class, 'typing'])->name('chat.typing');
