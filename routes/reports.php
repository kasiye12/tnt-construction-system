<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ReportExportController;
use Illuminate\Support\Facades\Route;

// Daily Reports CRUD
Route::resource('reports', DailyReportController::class);

// Report approval
Route::post('reports/{report}/approve', [DailyReportController::class, 'approve'])->name('reports.approve');
Route::post('reports/{report}/reject', [DailyReportController::class, 'reject'])->name('reports.reject');

// Report Export
Route::get('reports/export', [ReportExportController::class, 'index'])->name('reports.export');
Route::get('reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
Route::get('reports/export/pdf', [ReportExportController::class, 'exportPDF'])->name('reports.export.pdf');
Route::get('reports/{id}/print', [ReportExportController::class, 'printReport'])->name('reports.print');
