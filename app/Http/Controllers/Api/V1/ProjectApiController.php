<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectApiController extends Controller
{
    public function index()
    {
        $projects = Project::with(['manager', 'sites'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    public function show($id)
    {
        $project = Project::with(['manager', 'sites', 'dailyReports'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $project
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:projects',
            'location' => 'required|string',
            'manager_id' => 'required|exists:users,id',
            'status' => 'required|in:planning,active,on_hold,completed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric',
        ]);

        $validated['uuid'] = Str::uuid();
        $project = Project::create($validated);

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'Project created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $project,
            'message' => 'Project updated successfully'
        ]);
    }

    public function destroy($id)
    {
        Project::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }
}
