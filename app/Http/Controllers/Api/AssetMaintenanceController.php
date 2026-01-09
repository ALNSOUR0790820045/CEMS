<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetMaintenance;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetMaintenance::with(['fixedAsset', 'vendor']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        if ($request->has('fixed_asset_id')) {
            $query->where('fixed_asset_id', $request->fixed_asset_id);
        }

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        $maintenances = $query->orderBy('maintenance_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $maintenances
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fixed_asset_id' => 'required|exists:fixed_assets,id',
            'maintenance_type' => 'required|in:preventive,corrective,emergency',
            'maintenance_date' => 'required|date',
            'description' => 'required|string',
            'vendor_id' => 'nullable|exists:vendors,id',
            'cost' => 'nullable|numeric|min:0',
            'is_capitalized' => 'boolean',
            'next_maintenance_date' => 'nullable|date|after:maintenance_date',
            'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled',
            'performed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['company_id'] = auth()->user()->company_id;
            $validated['status'] = $validated['status'] ?? 'scheduled';
            $validated['cost'] = $validated['cost'] ?? 0;
            $validated['is_capitalized'] = $validated['is_capitalized'] ?? false;

            $maintenance = AssetMaintenance::create($validated);

            // If capitalized, add cost to asset value
            if ($maintenance->is_capitalized && $maintenance->cost > 0) {
                $asset = FixedAsset::findOrFail($maintenance->fixed_asset_id);
                $asset->update([
                    'acquisition_cost' => $asset->acquisition_cost + $maintenance->cost,
                    'net_book_value' => $asset->net_book_value + $maintenance->cost,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset maintenance created successfully',
                'data' => $maintenance->load(['fixedAsset', 'vendor'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset maintenance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $maintenance = AssetMaintenance::with([
            'fixedAsset.category',
            'fixedAsset.currency',
            'vendor'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $maintenance
        ]);
    }

    public function update(Request $request, $id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);

        $validated = $request->validate([
            'maintenance_type' => 'sometimes|required|in:preventive,corrective,emergency',
            'maintenance_date' => 'sometimes|required|date',
            'description' => 'sometimes|required|string',
            'vendor_id' => 'nullable|exists:vendors,id',
            'cost' => 'nullable|numeric|min:0',
            'is_capitalized' => 'boolean',
            'next_maintenance_date' => 'nullable|date',
            'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled',
            'performed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Handle cost capitalization changes
            $oldCost = $maintenance->cost;
            $oldCapitalized = $maintenance->is_capitalized;
            $newCost = $validated['cost'] ?? $oldCost;
            $newCapitalized = $validated['is_capitalized'] ?? $oldCapitalized;

            $maintenance->update($validated);

            // Adjust asset value if capitalization changed
            if ($oldCapitalized !== $newCapitalized || ($newCapitalized && $oldCost !== $newCost)) {
                $asset = FixedAsset::findOrFail($maintenance->fixed_asset_id);
                $costDifference = 0;

                if ($oldCapitalized && !$newCapitalized) {
                    // Remove old cost
                    $costDifference = -$oldCost;
                } elseif (!$oldCapitalized && $newCapitalized) {
                    // Add new cost
                    $costDifference = $newCost;
                } elseif ($oldCapitalized && $newCapitalized) {
                    // Adjust cost difference
                    $costDifference = $newCost - $oldCost;
                }

                if ($costDifference !== 0) {
                    $asset->update([
                        'acquisition_cost' => $asset->acquisition_cost + $costDifference,
                        'net_book_value' => $asset->net_book_value + $costDifference,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset maintenance updated successfully',
                'data' => $maintenance->fresh(['fixedAsset', 'vendor'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset maintenance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);

        // Cannot delete completed maintenance
        if ($maintenance->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete completed maintenance'
            ], 422);
        }

        try {
            $maintenance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset maintenance deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset maintenance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function scheduled(Request $request)
    {
        $query = AssetMaintenance::with(['fixedAsset', 'vendor'])
            ->where('status', 'scheduled')
            ->whereNotNull('next_maintenance_date');

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        // Filter by upcoming days
        $days = $request->input('days', 30);
        $query->where('next_maintenance_date', '<=', now()->addDays($days))
              ->where('next_maintenance_date', '>=', now());

        $maintenances = $query->orderBy('next_maintenance_date')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $maintenances
        ]);
    }

    public function complete(Request $request, $id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);

        if ($maintenance->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance is already completed'
            ], 422);
        }

        if ($maintenance->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot complete cancelled maintenance'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $maintenance->update([
                'status' => 'completed',
                'maintenance_date' => now(),
            ]);

            // Update asset status if it was under maintenance
            $asset = $maintenance->fixedAsset;
            if ($asset->status === 'under_maintenance') {
                $asset->update(['status' => 'active']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset maintenance completed successfully',
                'data' => $maintenance->fresh(['fixedAsset'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete asset maintenance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
