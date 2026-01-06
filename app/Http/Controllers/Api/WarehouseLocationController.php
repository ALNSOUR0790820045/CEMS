<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;

class WarehouseLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WarehouseLocation::with(['warehouse', 'parentLocation']);
        
        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        if ($request->has('location_type')) {
            $query->where('location_type', $request->location_type);
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        
        $locations = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'location_code' => 'required|string|max:255',
            'location_name' => 'required|string|max:255',
            'location_type' => 'required|in:zone,rack,bin,shelf',
            'parent_location_id' => 'nullable|exists:warehouse_locations,id',
            'capacity' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $location = WarehouseLocation::create($validated);
        $location->load(['warehouse', 'parentLocation']);

        return response()->json([
            'success' => true,
            'message' => 'Warehouse location created successfully',
            'data' => $location,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(WarehouseLocation $warehouseLocation)
    {
        $warehouseLocation->load(['warehouse', 'parentLocation', 'childLocations', 'stock']);
        
        return response()->json([
            'success' => true,
            'data' => $warehouseLocation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WarehouseLocation $warehouseLocation)
    {
        $validated = $request->validate([
            'location_code' => 'sometimes|string|max:255',
            'location_name' => 'sometimes|string|max:255',
            'location_type' => 'sometimes|in:zone,rack,bin,shelf',
            'parent_location_id' => 'nullable|exists:warehouse_locations,id',
            'capacity' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $warehouseLocation->update($validated);
        $warehouseLocation->load(['warehouse', 'parentLocation']);

        return response()->json([
            'success' => true,
            'message' => 'Warehouse location updated successfully',
            'data' => $warehouseLocation,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WarehouseLocation $warehouseLocation)
    {
        $warehouseLocation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Warehouse location deleted successfully',
        ]);
    }
}
