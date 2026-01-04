<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use App\Models\AssetDisposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FixedAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FixedAsset::with(['company', 'depreciations']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('asset_category', $request->category);
        }

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $assets = $query->paginate($request->get('per_page', 15));

        return response()->json($assets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_category' => ['required', Rule::in(['building', 'equipment', 'vehicle', 'furniture', 'computer', 'other'])],
            'asset_type' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|integer',
            'serial_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer',
            'custodian_id' => 'nullable|integer',
            'depreciation_method' => ['required', Rule::in(['straight_line', 'declining_balance', 'units_of_production'])],
            'useful_life_years' => 'required|integer|min:1',
            'salvage_value' => 'nullable|numeric|min:0',
            'gl_asset_account_id' => 'nullable|integer',
            'gl_depreciation_account_id' => 'nullable|integer',
            'gl_accumulated_depreciation_account_id' => 'nullable|integer',
            'warranty_expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        // Generate asset code
        $validated['asset_code'] = FixedAsset::generateAssetCode();

        $asset = FixedAsset::create($validated);

        return response()->json($asset->load(['company', 'depreciations']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FixedAsset $fixedAsset)
    {
        return response()->json($fixedAsset->load(['company', 'depreciations', 'disposal']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FixedAsset $fixedAsset)
    {
        $validated = $request->validate([
            'asset_name' => 'sometimes|required|string|max:255',
            'asset_category' => ['sometimes', 'required', Rule::in(['building', 'equipment', 'vehicle', 'furniture', 'computer', 'other'])],
            'asset_type' => 'nullable|string|max:255',
            'purchase_date' => 'sometimes|required|date',
            'purchase_cost' => 'sometimes|required|numeric|min:0',
            'supplier_id' => 'nullable|integer',
            'serial_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'department_id' => 'nullable|integer',
            'custodian_id' => 'nullable|integer',
            'depreciation_method' => ['sometimes', 'required', Rule::in(['straight_line', 'declining_balance', 'units_of_production'])],
            'useful_life_years' => 'sometimes|required|integer|min:1',
            'salvage_value' => 'nullable|numeric|min:0',
            'status' => ['sometimes', Rule::in(['active', 'disposed', 'under_maintenance', 'retired'])],
            'gl_asset_account_id' => 'nullable|integer',
            'gl_depreciation_account_id' => 'nullable|integer',
            'gl_accumulated_depreciation_account_id' => 'nullable|integer',
            'warranty_expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $fixedAsset->update($validated);

        return response()->json($fixedAsset->load(['company', 'depreciations']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FixedAsset $fixedAsset)
    {
        // Check if asset has been disposed
        if ($fixedAsset->disposal) {
            return response()->json(['error' => 'Cannot delete disposed asset'], 422);
        }

        $fixedAsset->delete();

        return response()->json(['message' => 'Asset deleted successfully'], 200);
    }

    /**
     * Calculate depreciation for assets
     */
    public function calculateDepreciation(Request $request)
    {
        $validated = $request->validate([
            'period_date' => 'required|date',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:fixed_assets,id',
        ]);

        $query = FixedAsset::where('status', 'active');

        if (isset($validated['asset_ids'])) {
            $query->whereIn('id', $validated['asset_ids']);
        }

        $assets = $query->get();
        $results = [];

        DB::beginTransaction();
        try {
            foreach ($assets as $asset) {
                // Check if depreciation already exists for this period
                $existingDepreciation = AssetDepreciation::where('fixed_asset_id', $asset->id)
                    ->where('period_date', $validated['period_date'])
                    ->first();

                if ($existingDepreciation) {
                    $results[] = [
                        'asset_id' => $asset->id,
                        'asset_code' => $asset->asset_code,
                        'status' => 'skipped',
                        'message' => 'Depreciation already calculated for this period',
                    ];
                    continue;
                }

                $depreciationAmount = $asset->calculateDepreciation($validated['period_date']);
                $newAccumulatedDepreciation = $asset->accumulated_depreciation + $depreciationAmount;
                $newBookValue = $asset->purchase_cost - $newAccumulatedDepreciation;

                // Create depreciation record
                $depreciation = AssetDepreciation::create([
                    'fixed_asset_id' => $asset->id,
                    'period_date' => $validated['period_date'],
                    'depreciation_amount' => $depreciationAmount,
                    'accumulated_depreciation' => $newAccumulatedDepreciation,
                    'book_value' => $newBookValue,
                    'posted' => false,
                ]);

                // Update asset accumulated depreciation
                $asset->update(['accumulated_depreciation' => $newAccumulatedDepreciation]);

                $results[] = [
                    'asset_id' => $asset->id,
                    'asset_code' => $asset->asset_code,
                    'status' => 'success',
                    'depreciation_amount' => $depreciationAmount,
                    'accumulated_depreciation' => $newAccumulatedDepreciation,
                    'book_value' => $newBookValue,
                ];
            }

            DB::commit();
            return response()->json([
                'message' => 'Depreciation calculated successfully',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error calculating depreciation: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Dispose an asset
     */
    public function dispose(Request $request, FixedAsset $fixedAsset)
    {
        // Check if already disposed
        if ($fixedAsset->disposal) {
            return response()->json(['error' => 'Asset already disposed'], 422);
        }

        $validated = $request->validate([
            'disposal_date' => 'required|date',
            'disposal_type' => ['required', Rule::in(['sale', 'scrap', 'donation', 'trade_in'])],
            'disposal_amount' => 'nullable|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['fixed_asset_id'] = $fixedAsset->id;
            $validated['book_value_at_disposal'] = $fixedAsset->book_value;
            $validated['company_id'] = $fixedAsset->company_id;

            $disposal = AssetDisposal::create($validated);

            // Update asset status
            $fixedAsset->update(['status' => 'disposed']);

            DB::commit();
            return response()->json([
                'message' => 'Asset disposed successfully',
                'disposal' => $disposal,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error disposing asset: ' . $e->getMessage()], 500);
        }
    }
}
