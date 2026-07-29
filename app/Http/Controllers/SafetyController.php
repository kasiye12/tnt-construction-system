<?php

namespace App\Http\Controllers;

use App\Models\SafetyIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SafetyController extends Controller
{
    public function index()
    {
        $incidents = SafetyIncident::with(['reportedBy', 'site', 'project'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => SafetyIncident::count(),
            'minor' => SafetyIncident::where('severity', 'minor')->count(),
            'moderate' => SafetyIncident::where('severity', 'moderate')->count(),
            'major' => SafetyIncident::where('severity', 'major')->count(),
            'open' => SafetyIncident::whereIn('status', ['reported', 'investigating'])->count(),
        ];

        return view('safety.index', compact('incidents', 'stats'));
    }

    public function create()
    {
        $sites = \App\Models\Site::where('status', 'active')->get();
        $projects = \App\Models\Project::where('status', 'active')->get();
        return view('safety.create', compact('sites', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'site_id' => 'required|exists:sites,id',
            'incident_datetime' => 'required|date',
            'severity' => 'required|in:minor,moderate,major,fatal',
            'type' => 'required|in:injury,near_miss,property_damage,environmental,equipment_failure,other',
            'description' => 'required|string',
            'immediate_actions' => 'nullable|string',
            'affected_persons' => 'nullable|string',
            'injuries_sustained' => 'nullable|string',
            'medical_treatment_required' => 'boolean',
            'work_stoppage' => 'boolean',
        ]);

        $validated['uuid'] = Str::uuid();
        $validated['incident_number'] = 'INC-' . date('Ymd') . '-' . Str::random(4);
        $validated['reported_by'] = Auth::id();
        $validated['status'] = 'reported';

        $incident = SafetyIncident::create($validated);

        return redirect()->route('safety.index')
            ->with('success', 'Safety incident reported successfully!');
    }

    public function show($id)
    {
        $incident = SafetyIncident::with(['reportedBy', 'investigatedBy', 'site', 'project'])
            ->findOrFail($id);

        return view('safety.show', compact('incident'));
    }

    public function investigate(Request $request, $id)
    {
        $incident = SafetyIncident::findOrFail($id);

        $request->validate([
            'root_cause' => 'required|string',
            'corrective_actions' => 'required|string',
            'preventive_measures' => 'nullable|string',
        ]);

        $incident->update([
            'status' => 'investigating',
            'investigated_by' => Auth::id(),
            'root_cause' => $request->root_cause,
            'corrective_actions' => $request->corrective_actions,
            'preventive_measures' => $request->preventive_measures,
        ]);

        return back()->with('success', 'Investigation submitted!');
    }

    public function resolve($id)
    {
        $incident = SafetyIncident::findOrFail($id);
        $incident->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Incident resolved!');
    }
}
