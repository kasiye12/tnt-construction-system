<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $sites = Site::with(['project', 'supervisor'])
            ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(12);

        $projects = Project::all();
        return view('sites.index', compact('sites', 'projects'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $supervisors = User::where('status', 'active')->get();
        return view('sites.create', compact('projects', 'supervisors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'site_name' => 'required|string|max:255',
            'site_code' => 'required|string|unique:sites',
            'supervisor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,active,inactive,completed',
            'type' => 'required|in:main_site,sub_site,temporary',
            'address' => 'nullable|string|max:500',
            'area_sqm' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'max_workers' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['uuid'] = Str::uuid();
        
        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site created successfully!');
    }

    public function show(Site $site)
    {
        $site->load(['project', 'supervisor', 'dailyReports' => function($q) {
            $q->latest()->take(10);
        }, 'workers']);
        
        return view('sites.show', compact('site'));
    }

    public function edit(Site $site)
    {
        $projects = Project::all();
        $supervisors = User::where('status', 'active')->get();
        return view('sites.edit', compact('site', 'projects', 'supervisors'));
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'site_name' => 'required|string|max:255',
            'site_code' => 'required|string|unique:sites,site_code,' . $site->id,
            'supervisor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,active,inactive,completed',
            'type' => 'required|in:main_site,sub_site,temporary',
            'address' => 'nullable|string|max:500',
            'area_sqm' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'max_workers' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $site->update($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site updated successfully!');
    }

    public function destroy(Site $site)
    {
        $site->delete();
        return redirect()->route('sites.index')
            ->with('success', 'Site deleted!');
    }
}
