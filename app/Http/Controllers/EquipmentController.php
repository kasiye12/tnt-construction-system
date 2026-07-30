<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $equipment = Equipment::with('currentSite')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->site_id, fn($q) => $q->where('current_site_id', $request->site_id))
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => Equipment::count(),
            'available' => Equipment::where('status', 'available')->count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'repair' => Equipment::where('status', 'repair')->count(),
        ];

        $sites = Site::where('status', 'active')->get();
        $types = ['excavator', 'bulldozer', 'crane', 'concrete_mixer', 'generator', 'compressor', 'truck', 'other'];

        return view('equipment.index', compact('equipment', 'stats', 'sites', 'types'));
    }

    public function create()
    {
        $sites = Site::where('status', 'active')->get();
        return view('equipment.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_code' => 'required|string|unique:equipment',
            'type' => 'required|string',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|unique:equipment',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,in_use,maintenance,repair,retired',
            'current_site_id' => 'nullable|exists:sites,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['uuid'] = Str::uuid();
        Equipment::create($validated);

        return redirect()->route('equipment.index')->with('success', '✅ Equipment added!');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['currentSite', 'usageLogs' => fn($q) => $q->latest()->take(10)]);
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $sites = Site::all();
        return view('equipment.edit', compact('equipment', 'sites'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_code' => 'required|unique:equipment,equipment_code,' . $equipment->id,
            'type' => 'required|string',
            'status' => 'required|in:available,in_use,maintenance,repair,retired',
            'current_site_id' => 'nullable|exists:sites,id',
            'notes' => 'nullable|string',
        ]);

        $equipment->update($validated);
        return redirect()->route('equipment.index')->with('success', '✅ Equipment updated!');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipment.index')->with('success', '✅ Equipment deleted!');
    }
}
