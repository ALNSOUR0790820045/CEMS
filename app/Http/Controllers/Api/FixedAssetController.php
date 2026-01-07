<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = FixedAsset::with([
            'category', 
            'subcategory', 
            'currency', 
            'location', 
            'department', 
            'project',
            'assignedTo',
            'vendor'
        ]);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        $assets = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $assets
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:asset_categories,id',
            'subcategory_id' => 'nullable|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:fixed_assets',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'useful_life_years' => 'nullable|integer|min:0',
            'useful_life_months' => 'nullable|integer|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'depreciation_method' => 'required|in:straight_line,declining_balance,units_of_production',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'location_id' => 'nullable|exists:warehouse_locations,id',
            'department_id' => 'nullable|exists:departments,id',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'warranty_expiry_date' => 'nullable|date',
            'insurance_policy_number' => 'nullable|string|max:255',
            'insurance_expiry_date' => 'nullable|date',
            'gl_asset_account_id' => 'nullable|exists:gl_accounts,id',
            'gl_depreciation_account_id' => 'nullable|exists:gl_accounts,id',
            'gl_accumulated_depreciation_account_id' => 'nullable|exists:gl_accounts,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['company_id'] = auth()->user()->company_id;
            $validated['status'] = 'active';
            $validated['accumulated_depreciation'] = 0;

            $asset = FixedAsset::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fixed asset created successfully',
                'data' => $asset->load(['category', 'currency', 'location', 'department'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create fixed asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $asset = FixedAsset::with([
            'category',
            'subcategory',
            'currency',
            'location',
            'department',
            'project',
            'assignedTo',
            'vendor',
            'purchaseOrder',
            'glAssetAccount',
            'glDepreciationAccount',
            'glAccumulatedDepreciationAccount',
            'depreciations' => function($query) {
                $query->orderBy('depreciation_date', 'desc')->limit(12);
            },
            'maintenances' => function($query) {
                $query->orderBy('maintenance_date', 'desc')->limit(10);
            },
            'transfers' => function($query) {
                $query->orderBy('transfer_date', 'desc')->limit(10);
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $asset
        ]);
    }

    public function update(Request $request, $id)
    {
        $asset = FixedAsset::findOrFail($id);

        $validated = $request->validate([
            'asset_name' => 'sometimes|required|string|max:255',
            'asset_name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:asset_categories,id',
            'subcategory_id' => 'nullable|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:fixed_assets,barcode,' . $id,
            'location_id' => 'nullable|exists:warehouse_locations,id',
            'department_id' => 'nullable|exists:departments,id',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'warranty_expiry_date' => 'nullable|date',
            'insurance_policy_number' => 'nullable|string|max:255',
            'insurance_expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:active,disposed,sold,written_off,under_maintenance'
        ]);

        try {
            $asset->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Fixed asset updated successfully',
                'data' => $asset->fresh(['category', 'currency', 'location', 'department'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fixed asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $asset = FixedAsset::findOrFail($id);

        try {
            $asset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fixed asset deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fixed asset',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function history($id)
    {
        $asset = FixedAsset::findOrFail($id);

        $history = [
            'depreciations' => $asset->depreciations()->orderBy('depreciation_date', 'desc')->get(),
            'maintenances' => $asset->maintenances()->orderBy('maintenance_date', 'desc')->get(),
            'transfers' => $asset->transfers()->with(['fromLocation', 'toLocation', 'fromDepartment', 'toDepartment'])->orderBy('transfer_date', 'desc')->get(),
            'disposals' => $asset->disposals()->orderBy('disposal_date', 'desc')->get(),
            'revaluations' => $asset->revaluations()->orderBy('revaluation_date', 'desc')->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function depreciationSchedule($id)
    {
        $asset = FixedAsset::findOrFail($id);

        $schedule = [];
        $remainingValue = $asset->acquisition_cost - $asset->accumulated_depreciation;
        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();
        
        $totalMonths = ($asset->useful_life_years * 12) + ($asset->useful_life_months ?? 0);
        $currentDate = now();

        for ($i = 0; $i < $totalMonths && $remainingValue > $asset->salvage_value; $i++) {
            $depreciationAmount = min($monthlyDepreciation, $remainingValue - $asset->salvage_value);
            $remainingValue -= $depreciationAmount;

            $schedule[] = [
                'period' => $i + 1,
                'date' => $currentDate->copy()->addMonths($i)->format('Y-m-d'),
                'depreciation_amount' => round($depreciationAmount, 2),
                'accumulated_depreciation' => round($asset->acquisition_cost - $remainingValue, 2),
                'net_book_value' => round($remainingValue, 2),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'asset' => $asset,
                'schedule' => $schedule
            ]
        ]);
    }

    public function calculateDepreciation(Request $request, $id)
    {
        $asset = FixedAsset::findOrFail($id);

        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000',
        ]);

        try {
            DB::beginTransaction();

            // Check if depreciation already exists for this period
            $existingDepreciation = AssetDepreciation::where('fixed_asset_id', $asset->id)
                ->where('period_month', $validated['period_month'])
                ->where('period_year', $validated['period_year'])
                ->first();

            if ($existingDepreciation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Depreciation already calculated for this period'
                ], 422);
            }

            $monthlyDepreciation = $asset->calculateMonthlyDepreciation();
            $newAccumulatedDepreciation = $asset->accumulated_depreciation + $monthlyDepreciation;
            $newNetBookValue = $asset->acquisition_cost - $newAccumulatedDepreciation;

            // Create depreciation record
            $depreciation = AssetDepreciation::create([
                'fixed_asset_id' => $asset->id,
                'depreciation_date' => now(),
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
                'depreciation_amount' => $monthlyDepreciation,
                'accumulated_depreciation' => $newAccumulatedDepreciation,
                'net_book_value' => $newNetBookValue,
                'is_posted' => false,
                'company_id' => auth()->user()->company_id,
            ]);

            // Update asset
            $asset->update([
                'accumulated_depreciation' => $newAccumulatedDepreciation,
                'net_book_value' => $newNetBookValue,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Depreciation calculated successfully',
                'data' => $depreciation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate depreciation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
