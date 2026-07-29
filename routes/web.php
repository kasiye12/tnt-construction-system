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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    
    // Sites
    Route::resource('sites', SiteController::class);
    
    // Reports - Custom routes BEFORE resource
    Route::get('/reports/export', [ReportExportController::class, 'index'])->name('reports.export');
    Route::get('/reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportExportController::class, 'exportPDF'])->name('reports.export.pdf');
    Route::get('/reports/{report}/print', [ReportExportController::class, 'printReport'])->name('reports.print');
    Route::post('/reports/{report}/approve', [DailyReportController::class, 'approve'])->name('reports.approve');
    Route::post('/reports/{report}/reject', [DailyReportController::class, 'reject'])->name('reports.reject');
    // Resource should be LAST
    Route::resource('reports', DailyReportController::class);
    
    // Users
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);
    
    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{channel}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{channel}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/create-channel', [ChatController::class, 'createChannel'])->name('chat.create');
    Route::post('/chat/message/{message}/pin', [ChatController::class, 'pinMessage'])->name('chat.pin');
    
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
});

require __DIR__.'/auth.php';

// Chat routes - make sure these are BEFORE the show route
Route::post('/chat/create-channel', [App\Http\Controllers\ChatController::class, 'createChannel'])->name('chat.create');

// Direct chat route
Route::post('/chat/direct', [App\Http\Controllers\ChatController::class, 'startDirectChat'])->name('chat.direct');
