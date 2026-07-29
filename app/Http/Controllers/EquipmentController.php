<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::with('currentSite')->paginate(12);
        $stats = [
            'total' => Equipment::count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'available' => Equipment::where('status', 'available')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
        ];
        return view('equipment.index', compact('equipment', 'stats'));
    }

    public function create()
    {
        $sites = \App\Models\Site::where('status', 'active')->get();
        return view('equipment.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_code' => 'required|unique:equipment',
            'type' => 'required|string',
            'status' => 'required|in:available,in_use,maintenance,repair,retired',
            'current_site_id' => 'nullable|exists:sites,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['uuid'] = Str::uuid();
        Equipment::create($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment added successfully!');
    }
}
