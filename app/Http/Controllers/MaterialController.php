<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $materials = Material::with('category')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Material::count(),
            'in_stock' => Material::where('status', 'active')->where('current_stock', '>', 0)->count(),
            'low_stock' => Material::whereColumn('current_stock', '<=', 'minimum_stock')->where('current_stock', '>', 0)->count(),
            'out_of_stock' => Material::where('current_stock', 0)->count(),
            'total_value' => Material::sum(\DB::raw('current_stock * unit_price')),
        ];

        $categories = MaterialCategory::all();

        return view('materials.index', compact('materials', 'stats', 'categories'));
    }

    public function create()
    {
        $categories = MaterialCategory::all();
        return view('materials.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'material_code' => 'required|unique:materials',
            'category_id' => 'nullable|exists:material_categories,id',
            'unit' => 'required|string',
            'unit_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'supplier_name' => 'nullable|string',
            'storage_location' => 'nullable|string',
        ]);

        $validated['uuid'] = Str::uuid();
        $validated['status'] = $validated['current_stock'] > 0 ? 'active' : 'out_of_stock';
        Material::create($validated);

        return redirect()->route('materials.index')->with('success', '✅ Material added!');
    }

    public function show(Material $material)
    {
        $material->load(['category', 'transactions' => fn($q) => $q->latest()->take(10)]);
        return view('materials.show', compact('material'));
    }
}
