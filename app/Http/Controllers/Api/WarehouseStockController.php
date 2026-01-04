<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseStockController extends Controller
{
    /**
     * Display a listing of warehouse stock.
     */
    public function index(Request $request)
    {
        $query = WarehouseStock::with(['warehouse', 'location', 'material']);
        
        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        
        if ($request->has('material_id')) {
            $query->where('material_id', $request->material_id);
        }
        
        $stock = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $stock,
        ]);
    }

    /**
     * Check stock availability for a material.
     */
    public function availability(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'required_quantity' => 'required|numeric|min:0',
        ]);

        $query = WarehouseStock::where('material_id', $validated['material_id']);
        
        if (isset($validated['warehouse_id'])) {
            $query->where('warehouse_id', $validated['warehouse_id']);
        }
        
        $totalAvailable = $query->sum(DB::raw('quantity - reserved_quantity'));
        
        $isAvailable = $totalAvailable >= $validated['required_quantity'];
        
        $stockDetails = $query->with(['warehouse', 'location'])
            ->where(DB::raw('quantity - reserved_quantity'), '>', 0)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'material_id' => $validated['material_id'],
                'required_quantity' => $validated['required_quantity'],
                'total_available' => $totalAvailable,
                'is_available' => $isAvailable,
                'stock_details' => $stockDetails,
            ],
        ]);
    }

    /**
     * Transfer stock between warehouses or locations.
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'from_location_id' => 'nullable|exists:warehouse_locations,id',
            'to_location_id' => 'nullable|exists:warehouse_locations,id',
            'quantity' => 'required|numeric|min:0.01',
            'batch_number' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Find source stock
            $sourceStock = WarehouseStock::where('warehouse_id', $validated['from_warehouse_id'])
                ->where('material_id', $validated['material_id'])
                ->where('location_id', $validated['from_location_id'])
                ->where(function($q) use ($validated) {
                    if (isset($validated['batch_number'])) {
                        $q->where('batch_number', $validated['batch_number']);
                    } else {
                        $q->whereNull('batch_number');
                    }
                })
                ->first();

            if (!$sourceStock || $sourceStock->available_quantity < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available for transfer',
                ], 400);
            }

            // Reduce source stock
            $sourceStock->quantity -= $validated['quantity'];
            $sourceStock->last_updated = now();
            $sourceStock->save();

            // Find or create destination stock
            $destinationStock = WarehouseStock::firstOrCreate(
                [
                    'warehouse_id' => $validated['to_warehouse_id'],
                    'location_id' => $validated['to_location_id'],
                    'material_id' => $validated['material_id'],
                    'batch_number' => $validated['batch_number'] ?? null,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'expiry_date' => $sourceStock->expiry_date,
                ]
            );

            // Increase destination stock
            $destinationStock->quantity += $validated['quantity'];
            $destinationStock->last_updated = now();
            $destinationStock->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock transferred successfully',
                'data' => [
                    'source' => $sourceStock->fresh(['warehouse', 'location', 'material']),
                    'destination' => $destinationStock->fresh(['warehouse', 'location', 'material']),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer stock: ' . $e->getMessage(),
            ], 500);
        }
    }
}
