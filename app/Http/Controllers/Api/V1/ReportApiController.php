<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ReportApiController extends Controller
{
    public function index(Request $request)
    {
        $reports = DailyReport::with(['site', 'submittedBy', 'project'])
            ->when($request->site_id, fn($q) => $q->where('site_id', $request->site_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date, fn($q) => $q->whereDate('report_date', $request->date))
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'report_date' => 'required|date',
            'workforce_count' => 'nullable|integer',
            'progress_percentage' => 'nullable|numeric',
            'summary_text' => 'nullable|string',
            'challenges_encountered' => 'nullable|string',
            'safety_incidents' => 'nullable|string',
            'material_deliveries' => 'nullable|string',
        ]);

        $site = Site::find($validated['site_id']);

        $report = DailyReport::create([
            'uuid' => Str::uuid(),
            'site_id' => $validated['site_id'],
            'project_id' => $site->project_id,
            'submitted_by' => Auth::id(),
            'report_date' => $validated['report_date'],
            'workforce_count' => $validated['workforce_count'] ?? 0,
            'progress_percentage' => $validated['progress_percentage'] ?? 0,
            'summary_text' => $validated['summary_text'],
            'challenges_encountered' => $validated['challenges_encountered'],
            'safety_incidents' => $validated['safety_incidents'],
            'material_deliveries' => $validated['material_deliveries'],
            'status' => 'submitted',
        ]);

        return response()->json([
            'success' => true,
            'data' => $report->load(['site', 'submittedBy']),
            'message' => 'Report submitted successfully'
        ], 201);
    }

    public function show($id)
    {
        $report = DailyReport::with(['site', 'submittedBy', 'approvedBy', 'project', 'attachments'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function todayStats()
    {
        $stats = [
            'total_reports' => DailyReport::whereDate('report_date', today())->count(),
            'total_workforce' => DailyReport::whereDate('report_date', today())->sum('workforce_count'),
            'average_progress' => round(DailyReport::whereDate('report_date', today())->avg('progress_percentage'), 2),
            'pending_approvals' => DailyReport::where('status', 'submitted')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
