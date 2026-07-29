<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('category')->paginate(15);
        $stats = [
            'total' => Material::count(),
            'low_stock' => Material::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock' => Material::where('current_stock', 0)->count(),
        ];
        return view('materials.index', compact('materials', 'stats'));
    }

    public function create()
    {
        $categories = \App\Models\MaterialCategory::all();
        return view('materials.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'material_code' => 'required|unique:materials',
            'unit' => 'required|string',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier_name' => 'nullable|string',
            'category_id' => 'nullable|exists:material_categories,id',
        ]);

        $validated['uuid'] = Str::uuid();
        Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material added successfully!');
    }
}
