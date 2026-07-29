<?php
// File: app/Http/Controllers/Api/V1/DailyReportController.php

namespace App\Http\Controllers\Api\V1;

use App\Events\DailyReportSubmitted;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\DailyReport;
use App\Services\DailyReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    protected $reportService;

    public function __construct(DailyReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request): JsonResponse
    {
        $reports = DailyReport::query()
            ->with(['site:id,site_name', 'submittedBy:id,full_name'])
            ->when($request->site_id, fn($q) => $q->where('site_id', $request->site_id))
            ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('report_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('report_date', '<=', $request->date_to))
            ->latest('report_date')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    public function store(StoreDailyReportRequest $request): JsonResponse
    {
        $report = $this->reportService->createReport(
            $request->validated(),
            Auth::user()
        );

        broadcast(new DailyReportSubmitted($report))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Daily report created successfully',
            'data' => $report->load(['site', 'submittedBy', 'attachments'])
        ], 201);
    }

    public function show(DailyReport $report): JsonResponse
    {
        $this->authorize('view', $report);

        return response()->json([
            'success' => true,
            'data' => $report->load([
                'site:id,site_name,location_coordinates',
                'submittedBy:id,full_name,phone_number',
                'approvedBy:id,full_name',
                'attachments'
            ])
        ]);
    }

    public function update(UpdateDailyReportRequest $request, DailyReport $report): JsonResponse
    {
        $this->authorize('update', $report);

        if ($report->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update an approved report'
            ], 403);
        }

        $report->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Report updated successfully',
            'data' => $report->fresh()
        ]);
    }

    public function approve(DailyReport $report): JsonResponse
    {
        $this->authorize('approve', $report);

        $report->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report approved successfully'
        ]);
    }

    public function reject(Request $request, DailyReport $report): JsonResponse
    {
        $this->authorize('approve', $report);

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $report->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report rejected successfully'
        ]);
    }

    public function getDailyStats(Request $request): JsonResponse
    {
        $stats = $this->reportService->getDailyStatistics(
            $request->project_id,
            $request->date
        );

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}