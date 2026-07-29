<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\WorkerCheckin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    public function syncReports(Request $request)
    {
        $request->validate([
            'reports' => 'required|array',
            'reports.*.site_id' => 'required|exists:sites,id',
            'reports.*.report_date' => 'required|date',
            'reports.*.workforce_count' => 'nullable|integer',
            'reports.*.summary_text' => 'nullable|string',
        ]);

        $syncedReports = [];
        
        foreach ($request->reports as $reportData) {
            $reportData['uuid'] = Str::uuid();
            $reportData['project_id'] = \App\Models\Site::find($reportData['site_id'])->project_id;
            $reportData['submitted_by'] = $request->user()->id;
            $reportData['status'] = 'submitted';
            $reportData['is_offline_submission'] = true;
            
            $report = DailyReport::create($reportData);
            $syncedReports[] = $report;
        }

        return response()->json([
            'success' => true,
            'data' => $syncedReports,
            'message' => count($syncedReports) . ' reports synced successfully'
        ]);
    }

    public function syncCheckins(Request $request)
    {
        $request->validate([
            'checkins' => 'required|array',
            'checkins.*.site_id' => 'required|exists:sites,id',
            'checkins.*.check_in_time' => 'required|date',
        ]);

        $syncedCheckins = [];
        
        foreach ($request->checkins as $checkinData) {
            $checkinData['uuid'] = Str::uuid();
            $checkinData['user_id'] = $request->user()->id;
            $checkinData['check_in_method'] = 'mobile_app';
            $checkinData['status'] = 'checked_in';
            
            $checkin = WorkerCheckin::create($checkinData);
            $syncedCheckins[] = $checkin;
        }

        return response()->json([
            'success' => true,
            'data' => $syncedCheckins,
            'message' => count($syncedCheckins) . ' check-ins synced successfully'
        ]);
    }
}
