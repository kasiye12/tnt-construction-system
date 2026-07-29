<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Site;
use App\Models\DailyReport;
use App\Models\WorkerCheckin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MobileController extends Controller
{
    public function home()
    {
        $user = Auth::user()->load('site');
        $todayCheckin = WorkerCheckin::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->first();

        $todayReport = DailyReport::where('submitted_by', $user->id)
            ->whereDate('report_date', today())
            ->first();

        $recentReports = DailyReport::where('submitted_by', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $projects = Project::whereHas('sites.workers', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        return view('mobile.home', compact(
            'user', 'todayCheckin', 'todayReport', 
            'recentReports', 'projects'
        ));
    }

    public function reports()
    {
        $reports = DailyReport::with(['site', 'project'])
            ->where('submitted_by', Auth::id())
            ->latest()
            ->paginate(15);

        return view('mobile.reports', compact('reports'));
    }

    public function createReport()
    {
        $sites = Site::whereHas('workers', function($q) {
            $q->where('user_id', Auth::id());
        })->get();

        if ($sites->isEmpty()) {
            $sites = Site::where('status', 'active')->get();
        }

        return view('mobile.report-create', compact('sites'));
    }

    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'workforce_count' => 'nullable|integer|min:0',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'summary_text' => 'nullable|string',
            'challenges' => 'nullable|string',
        ]);

        $site = Site::find($validated['site_id']);

        $report = DailyReport::create([
            'uuid' => Str::uuid(),
            'site_id' => $validated['site_id'],
            'project_id' => $site->project_id,
            'submitted_by' => Auth::id(),
            'report_date' => today(),
            'workforce_count' => $validated['workforce_count'] ?? 0,
            'progress_percentage' => $validated['progress_percentage'] ?? 0,
            'summary_text' => $validated['summary_text'],
            'challenges_encountered' => $validated['challenges'],
            'status' => 'submitted',
        ]);

        return redirect()->route('mobile.reports')
            ->with('success', 'Report submitted successfully!');
    }

    public function checkin()
    {
        $user = Auth::user()->load('site');
        $todayCheckin = WorkerCheckin::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->first();

        $recentCheckins = WorkerCheckin::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('mobile.checkin', compact('user', 'todayCheckin', 'recentCheckins'));
    }

    public function doCheckin(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();

        $existing = WorkerCheckin::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'checked_in')
            ->whereNull('check_out_time')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in at ' . $existing->check_in_time->format('H:i')
            ]);
        }

        $checkin = WorkerCheckin::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'site_id' => $user->site_id,
            'check_in_time' => now(),
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'check_in_method' => 'mobile_app',
            'status' => 'checked_in',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully!',
            'time' => now()->format('H:i')
        ]);
    }

    public function doCheckout(Request $request)
    {
        $checkin = WorkerCheckin::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->where('status', 'checked_in')
            ->whereNull('check_out_time')
            ->first();

        if (!$checkin) {
            return response()->json([
                'success' => false,
                'message' => 'No active check-in found'
            ]);
        }

        $hoursWorked = round($checkin->check_in_time->diffInHours(now()), 1);

        $checkin->update([
            'check_out_time' => now(),
            'status' => 'checked_out',
            'check_out_method' => 'mobile_app',
            'hours_worked' => $hoursWorked
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully!',
            'hours' => $hoursWorked
        ]);
    }

    public function profile()
    {
        $user = Auth::user()->load('site');
        return view('mobile.profile', compact('user'));
    }

    public function chat()
    {
        return view('mobile.chat');
    }
}
