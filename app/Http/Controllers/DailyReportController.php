<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Site;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = DailyReport::with(['site', 'submittedBy', 'project'])
            ->when($request->site_id, fn($q) => $q->where('site_id', $request->site_id))
            ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('report_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('report_date', '<=', $request->date_to))
            ->latest('report_date')
            ->paginate(15);

        $sites = Site::all();
        $projects = Project::all();

        return view('reports.index', compact('reports', 'sites', 'projects'));
    }

    public function create()
    {
        $sites = Site::where('status', 'active')->get();
        return view('reports.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'report_date' => 'required|date',
            'workforce_count' => 'nullable|integer|min:0',
            'subcontractor_count' => 'nullable|integer|min:0',
            'absent_count' => 'nullable|integer|min:0',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'summary_text' => 'nullable|string',
            'challenges_encountered' => 'nullable|string',
            'safety_incidents' => 'nullable|string',
            'material_deliveries' => 'nullable|string',
            'equipment_hours' => 'nullable|json',
            'weather_conditions' => 'nullable|json',
            'status' => 'required|in:draft,submitted',
        ]);

        $site = Site::findOrFail($validated['site_id']);
        
        $validated['uuid'] = Str::uuid();
        $validated['project_id'] = $site->project_id;
        $validated['submitted_by'] = Auth::id();

        DailyReport::create($validated);

        return redirect()->route('reports.index')
            ->with('success', 'Daily report submitted successfully!');
    }

    public function show(DailyReport $report)
    {
        $report->load(['site', 'submittedBy', 'approvedBy', 'project']);
        return view('reports.show', compact('report'));
    }

    public function edit(DailyReport $report)
    {
        $sites = Site::all();
        return view('reports.edit', compact('report', 'sites'));
    }

    public function update(Request $request, DailyReport $report)
    {
        if ($report->status === 'approved') {
            return back()->with('error', 'Cannot update approved report');
        }

        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'report_date' => 'required|date',
            'workforce_count' => 'nullable|integer|min:0',
            'subcontractor_count' => 'nullable|integer|min:0',
            'absent_count' => 'nullable|integer|min:0',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'summary_text' => 'nullable|string',
            'challenges_encountered' => 'nullable|string',
            'safety_incidents' => 'nullable|string',
            'material_deliveries' => 'nullable|string',
            'status' => 'required|in:draft,submitted',
        ]);

        $report->update($validated);

        return redirect()->route('reports.index')
            ->with('success', 'Report updated successfully!');
    }

    public function approve(DailyReport $report)
    {
        $report->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Report approved!');
    }

    public function reject(Request $request, DailyReport $report)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        
        $report->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Report rejected!');
    }

    public function destroy(DailyReport $report)
    {
        $report->delete();
        return redirect()->route('reports.index')
            ->with('success', 'Report deleted!');
    }
}
