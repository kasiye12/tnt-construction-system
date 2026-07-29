<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Site;
use App\Models\DailyReport;
use App\Models\User;
use App\Models\WorkerCheckin;
use App\Models\Equipment;
use App\Models\Material;
use App\Models\SafetyIncident;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        // Core Stats
        $stats = [
            'projects' => [
                'total' => Project::count(),
                'active' => Project::where('status', 'active')->count(),
                'completed' => Project::where('status', 'completed')->count(),
                'on_hold' => Project::where('status', 'on_hold')->count(),
                'total_budget' => Project::sum('budget'),
            ],
            'sites' => [
                'total' => Site::count(),
                'active' => Site::where('status', 'active')->count(),
                'avg_progress' => round(Site::avg('progress_percentage') ?? 0, 1),
            ],
            'workers' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'checked_in_today' => WorkerCheckin::whereDate('created_at', $today)
                    ->where('status', 'checked_in')
                    ->whereNull('check_out_time')
                    ->count(),
                'total_checkins_today' => WorkerCheckin::whereDate('created_at', $today)->count(),
            ],
            'reports' => [
                'today' => DailyReport::whereDate('report_date', $today)->count(),
                'this_week' => DailyReport::whereBetween('report_date', [$weekStart, $today])->count(),
                'this_month' => DailyReport::whereBetween('report_date', [$monthStart, $today])->count(),
                'pending' => DailyReport::where('status', 'submitted')->count(),
                'approved' => DailyReport::where('status', 'approved')->count(),
            ],
            'equipment' => [
                'total' => Equipment::count(),
                'in_use' => Equipment::where('status', 'in_use')->count(),
                'available' => Equipment::where('status', 'available')->count(),
                'maintenance' => Equipment::where('status', 'maintenance')->count(),
            ],
            'materials' => [
                'total' => Material::count(),
                'low_stock' => Material::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            ],
            'safety' => [
                'total_incidents' => SafetyIncident::count(),
                'open' => SafetyIncident::whereIn('status', ['reported', 'investigating'])->count(),
                'this_month' => SafetyIncident::whereMonth('created_at', $today->month)->count(),
            ],
        ];

        // Weekly report chart data
        $weeklyChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyChart[] = [
                'day' => $date->format('D'),
                'date' => $date->format('M d'),
                'count' => DailyReport::whereDate('report_date', $date)->count(),
                'checkins' => WorkerCheckin::whereDate('created_at', $date)->count(),
            ];
        }

        // Recent activities
        $recentReports = DailyReport::with(['site', 'submittedBy', 'project'])
            ->latest()
            ->take(8)
            ->get();

        $recentCheckins = WorkerCheckin::with(['user', 'site'])
            ->whereDate('created_at', $today)
            ->latest()
            ->take(8)
            ->get();

        $activeProjects = Project::with(['manager', 'sites'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        // Top sites by progress
        $topSites = Site::where('status', 'active')
            ->orderBy('progress_percentage', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'weeklyChart', 'recentReports', 
            'recentCheckins', 'activeProjects', 'topSites'
        ));
    }
}
