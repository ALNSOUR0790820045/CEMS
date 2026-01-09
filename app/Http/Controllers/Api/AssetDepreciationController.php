<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetDepreciation;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetDepreciationController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetDepreciation::with(['fixedAsset', 'postedBy']);

        // Apply filters
        if ($request->has('is_posted')) {
            $query->where('is_posted', $request->boolean('is_posted'));
        }

        if ($request->has('fixed_asset_id')) {
            $query->where('fixed_asset_id', $request->fixed_asset_id);
        }

        if ($request->has('period_month')) {
            $query->where('period_month', $request->period_month);
        }

        if ($request->has('period_year')) {
            $query->where('period_year', $request->period_year);
        }

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        $depreciations = $query->orderBy('depreciation_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $depreciations
        ]);
    }

    public function runMonthly(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000',
        ]);

        try {
            DB::beginTransaction();

            $companyId = auth()->user()->company_id;
            
            // Get all active assets for the company
            $assets = FixedAsset::where('company_id', $companyId)
                ->where('status', 'active')
                ->get();

            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($assets as $asset) {
                // Check if depreciation already exists
                $exists = AssetDepreciation::where('fixed_asset_id', $asset->id)
                    ->where('period_month', $validated['period_month'])
                    ->where('period_year', $validated['period_year'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Calculate depreciation
                $monthlyDepreciation = $asset->calculateMonthlyDepreciation();
                
                // Skip if no depreciation
                if ($monthlyDepreciation <= 0) {
                    $skipped++;
                    continue;
                }

                $newAccumulatedDepreciation = $asset->accumulated_depreciation + $monthlyDepreciation;
                $newNetBookValue = $asset->acquisition_cost - $newAccumulatedDepreciation;

                // Don't depreciate below salvage value
                if ($newNetBookValue < $asset->salvage_value) {
                    $monthlyDepreciation = $asset->net_book_value - $asset->salvage_value;
                    $newAccumulatedDepreciation = $asset->acquisition_cost - $asset->salvage_value;
                    $newNetBookValue = $asset->salvage_value;
                }

                if ($monthlyDepreciation > 0) {
                    // Create depreciation record
                    AssetDepreciation::create([
                        'fixed_asset_id' => $asset->id,
                        'depreciation_date' => now(),
                        'period_month' => $validated['period_month'],
                        'period_year' => $validated['period_year'],
                        'depreciation_amount' => $monthlyDepreciation,
                        'accumulated_depreciation' => $newAccumulatedDepreciation,
                        'net_book_value' => $newNetBookValue,
                        'is_posted' => false,
                        'company_id' => $companyId,
                    ]);

                    // Update asset
                    $asset->update([
                        'accumulated_depreciation' => $newAccumulatedDepreciation,
                        'net_book_value' => $newNetBookValue,
                    ]);

                    $created++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Monthly depreciation run completed',
                'data' => [
                    'created' => $created,
                    'skipped' => $skipped,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to run monthly depreciation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'depreciation_ids' => 'required|array',
            'depreciation_ids.*' => 'exists:asset_depreciations,id',
        ]);

        try {
            DB::beginTransaction();

            $depreciations = AssetDepreciation::whereIn('id', $validated['depreciation_ids'])
                ->where('is_posted', false)
                ->get();

            if ($depreciations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No unposted depreciations found'
                ], 422);
            }

            foreach ($depreciations as $depreciation) {
                // TODO: Create GL journal entry here
                // For now, just mark as posted
                $depreciation->update([
                    'is_posted' => true,
                    'posted_by_id' => auth()->id(),
                    'posted_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Depreciations posted successfully',
                'data' => [
                    'posted_count' => $depreciations->count()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to post depreciations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000',
        ]);

        $companyId = auth()->user()->company_id;
        
        // Get all active assets
        $assets = FixedAsset::with(['category', 'currency'])
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $preview = [];
        $totalDepreciation = 0;

        foreach ($assets as $asset) {
            // Check if already calculated
            $exists = AssetDepreciation::where('fixed_asset_id', $asset->id)
                ->where('period_month', $validated['period_month'])
                ->where('period_year', $validated['period_year'])
                ->exists();

            if ($exists) {
                continue;
            }

            $monthlyDepreciation = $asset->calculateMonthlyDepreciation();
            
            if ($monthlyDepreciation <= 0) {
                continue;
            }

            $newAccumulatedDepreciation = $asset->accumulated_depreciation + $monthlyDepreciation;
            $newNetBookValue = $asset->acquisition_cost - $newAccumulatedDepreciation;

            if ($newNetBookValue < $asset->salvage_value) {
                $monthlyDepreciation = $asset->net_book_value - $asset->salvage_value;
                $newNetBookValue = $asset->salvage_value;
            }

            if ($monthlyDepreciation > 0) {
                $preview[] = [
                    'asset_id' => $asset->id,
                    'asset_code' => $asset->asset_code,
                    'asset_name' => $asset->asset_name,
                    'category' => $asset->category->name ?? null,
                    'depreciation_amount' => round($monthlyDepreciation, 2),
                    'current_net_book_value' => round($asset->net_book_value, 2),
                    'new_net_book_value' => round($newNetBookValue, 2),
                ];

                $totalDepreciation += $monthlyDepreciation;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
                'assets' => $preview,
                'total_depreciation' => round($totalDepreciation, 2),
                'asset_count' => count($preview)
            ]
        ]);
    }
}
