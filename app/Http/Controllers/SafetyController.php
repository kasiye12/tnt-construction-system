<?php

namespace App\Http\Controllers;

use App\Models\SafetyIncident;
use App\Models\Site;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SafetyController extends Controller
{
    public function index(Request $request)
    {
        $incidents = SafetyIncident::with(['site', 'project', 'reportedBy'])
            ->when($request->severity, fn($q) => $q->where('severity', $request->severity))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => SafetyIncident::count(),
            'minor' => SafetyIncident::where('severity', 'minor')->count(),
            'moderate' => SafetyIncident::where('severity', 'moderate')->count(),
            'major' => SafetyIncident::where('severity', 'major')->count(),
            'fatal' => SafetyIncident::where('severity', 'fatal')->count(),
            'open' => SafetyIncident::whereIn('status', ['reported', 'investigating'])->count(),
            'resolved' => SafetyIncident::where('status', 'resolved')->count(),
        ];

        return view('safety.index', compact('incidents', 'stats'));
    }

    public function create()
    {
        $sites = Site::where('status', 'active')->get();
        $projects = Project::where('status', 'active')->get();
        return view('safety.create', compact('sites', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'site_id' => 'required|exists:sites,id',
            'incident_datetime' => 'required|date',
            'severity' => 'required|in:minor,moderate,major,fatal',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'required|string',
            'immediate_actions' => 'nullable|string',
            'affected_persons' => 'nullable|string',
        ]);

        $validated['uuid'] = Str::uuid();
        $validated['incident_number'] = 'INC-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $validated['reported_by'] = Auth::id();
        $validated['status'] = 'reported';

        // Send notification
        $notifService = new \App\Services\NotificationService();
        $notifService->notifyManagement('safety_incident', '🦺 Safety Incident Reported', 'New ' . $request->severity . ' severity incident at ' . \App\ModelsSite::find($request->site_id)->site_name, ['incident_id' => 'new']);
        SafetyIncident::create($validated);

        return redirect()->route('safety.index')->with('success', '✅ Incident reported!');
    }

    public function show(SafetyIncident $safety)
    {
        $safety->load(['reportedBy', 'site', 'project', 'investigatedBy']);
        return view('safety.show', ['incident' => $safety]);
    }

    public function resolve(Request $request, SafetyIncident $safety)
    {
        $safety->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'investigated_by' => Auth::id(),
            'root_cause' => $request->root_cause,
            'corrective_actions' => $request->corrective_actions,
            'preventive_measures' => $request->preventive_measures,
        ]);

        return back()->with('success', '✅ Incident resolved!');
    }
}
