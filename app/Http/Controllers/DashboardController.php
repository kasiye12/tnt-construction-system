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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $stats = [
            'projects' => [
                'total' => Project::count(),
                'active' => Project::where('status', 'active')->count(),
                'completed' => Project::where('status', 'completed')->count(),
            ],
            'sites' => [
                'total' => Site::count(),
                'active' => Site::where('status', 'active')->count(),
                'avg_progress' => round(Site::avg('progress_percentage') ?? 0, 1),
            ],
            'workers' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'checked_in_today' => WorkerCheckin::whereDate('created_at', $today)->where('status', 'checked_in')->whereNull('check_out_time')->count(),
                'total_checkins_today' => WorkerCheckin::whereDate('created_at', $today)->count(),
            ],
            'reports' => [
                'today' => DailyReport::whereDate('report_date', $today)->count(),
                'this_week' => DailyReport::whereBetween('report_date', [$weekStart, $today])->count(),
                'pending' => DailyReport::where('status', 'submitted')->count(),
                'approved' => DailyReport::where('status', 'approved')->count(),
            ],
            'equipment' => [
                'total' => Equipment::count(),
                'in_use' => Equipment::where('status', 'in_use')->count(),
            ],
            'materials' => [
                'total' => Material::count(),
                'low_stock' => Material::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            ],
            'safety' => [
                'total' => SafetyIncident::count(),
                'open' => SafetyIncident::whereIn('status', ['reported', 'investigating'])->count(),
            ],
        ];

        // Weekly chart
        $weeklyChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyChart[] = [
                'day' => $date->format('D'),
                'count' => DailyReport::whereDate('report_date', $date)->count(),
            ];
        }

        $recentReports = DailyReport::with(['site', 'submittedBy', 'project'])->latest()->take(8)->get();
        $recentCheckins = WorkerCheckin::with(['user', 'site'])->whereDate('created_at', $today)->latest()->take(8)->get();
        $activeProjects = Project::with(['manager', 'sites'])->where('status', 'active')->latest()->take(5)->get();
        $topSites = Site::where('status', 'active')->orderBy('progress_percentage', 'desc')->take(5)->get();
        
        $notifications = DB::table('notifications')
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', 'App\\Models\\User')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'weeklyChart', 'recentReports', 
            'recentCheckins', 'activeProjects', 'topSites', 'notifications'
        ));
    }
}
