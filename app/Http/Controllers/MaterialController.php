<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Material::with(['category', 'unit', 'currency']);

        // Filter by material type
        if ($request->has('material_type')) {
            $query->where('material_type', $request->material_type);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name or code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('material_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $materials = $query->paginate($request->get('per_page', 15));

        return response()->json($materials);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'required|in:raw_material,finished_goods,consumables,tools,equipment',
            'category_id' => 'nullable|exists:material_categories,id',
            'unit_id' => 'required|exists:units,id',
            'reorder_level' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'standard_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
            'image_path' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_stockable' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $material = Material::create($validated);

        return response()->json($material->load(['category', 'unit', 'currency']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $material = Material::with(['category', 'unit', 'currency', 'specifications', 'materialVendors.vendor', 'materialVendors.currency'])
            ->findOrFail($id);

        return response()->json($material);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'sometimes|required|in:raw_material,finished_goods,consumables,tools,equipment',
            'category_id' => 'nullable|exists:material_categories,id',
            'unit_id' => 'sometimes|required|exists:units,id',
            'reorder_level' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'standard_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
            'image_path' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_stockable' => 'boolean',
        ]);

        $material->update($validated);

        return response()->json($material->load(['category', 'unit', 'currency']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return response()->json(['message' => 'Material deleted successfully']);
    }

    /**
     * Get vendors for a specific material.
     */
    public function vendors($id)
    {
        $material = Material::findOrFail($id);
        $vendors = $material->materialVendors()
            ->with(['vendor', 'currency'])
            ->get();

        return response()->json($vendors);
    }

    /**
     * Add a vendor to a material.
     */
    public function addVendor(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'vendor_material_code' => 'nullable|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'lead_time_days' => 'nullable|integer|min:0',
            'min_order_quantity' => 'nullable|numeric|min:0',
            'is_preferred' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $materialVendor = $material->materialVendors()->create($validated);

        return response()->json($materialVendor->load(['vendor', 'currency']), 201);
    }
}
