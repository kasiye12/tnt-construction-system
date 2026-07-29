<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\DailyReport;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    public function index()
    {
        $projects = Project::where('status', 'active')->get();
        $reports = DailyReport::with(['site', 'project', 'submittedBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('reports.export', compact('projects', 'reports'));
    }

    public function exportExcel(Request $request)
    {
        if (!class_exists('Maatwebsite\Excel\Facades\Excel')) {
            return back()->with('error', 'Excel package not installed. Run: composer require maatwebsite/excel');
        }

        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $fileName = 'daily-reports-' . now()->format('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DailyReportExport(
                $request->project_id,
                $request->date_from,
                $request->date_to
            ),
            $fileName
        );
    }

    public function exportPDF(Request $request)
    {
        if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            return back()->with('error', 'PDF package not installed. Run: composer require barryvdh/laravel-dompdf');
        }

        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $reports = DailyReport::with(['site', 'submittedBy', 'project', 'approvedBy'])
            ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->date_from, fn($q) => $q->whereDate('report_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('report_date', '<=', $request->date_to))
            ->latest()
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('reports'));
        
        return $pdf->download('daily-reports-' . now()->format('Y-m-d') . '.pdf');
    }

    public function printReport($id)
    {
        if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            return back()->with('error', 'PDF package not installed.');
        }

        $report = DailyReport::with(['site', 'submittedBy', 'approvedBy', 'project'])
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.print', compact('report'));
        
        return $pdf->stream('report-' . $report->id . '.pdf');
    }
}
