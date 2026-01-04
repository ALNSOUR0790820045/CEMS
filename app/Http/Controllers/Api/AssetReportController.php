<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetReportController extends Controller
{
    /**
     * Get asset register report
     */
    public function assetRegister(Request $request)
    {
        $query = FixedAsset::with(['company']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('asset_category', $request->category);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('purchase_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('purchase_date', '<=', $request->to_date);
        }

        $assets = $query->get();

        $summary = [
            'total_assets' => $assets->count(),
            'total_cost' => $assets->sum('purchase_cost'),
            'total_accumulated_depreciation' => $assets->sum('accumulated_depreciation'),
            'total_book_value' => $assets->sum(function ($asset) {
                return $asset->book_value;
            }),
        ];

        return response()->json([
            'assets' => $assets,
            'summary' => $summary,
        ]);
    }

    /**
     * Get depreciation schedule report
     */
    public function depreciationSchedule(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'asset_id' => 'nullable|exists:fixed_assets,id',
        ]);

        $query = AssetDepreciation::with(['fixedAsset.company']);

        // Filter by asset
        if (isset($validated['asset_id'])) {
            $query->where('fixed_asset_id', $validated['asset_id']);
        }

        // Filter by company through asset
        if (isset($validated['company_id'])) {
            $query->whereHas('fixedAsset', function ($q) use ($validated) {
                $q->where('company_id', $validated['company_id']);
            });
        }

        // Filter by date range
        if (isset($validated['from_date'])) {
            $query->where('period_date', '>=', $validated['from_date']);
        }

        if (isset($validated['to_date'])) {
            $query->where('period_date', '<=', $validated['to_date']);
        }

        $depreciations = $query->orderBy('period_date', 'desc')->get();

        $summary = [
            'total_depreciation' => $depreciations->sum('depreciation_amount'),
            'total_accumulated_depreciation' => $depreciations->first()->accumulated_depreciation ?? 0,
        ];

        return response()->json([
            'depreciations' => $depreciations,
            'summary' => $summary,
        ]);
    }

    /**
     * Get asset valuation report
     */
    public function assetValuation(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'as_of_date' => 'nullable|date',
        ]);

        $asOfDate = $validated['as_of_date'] ?? now()->toDateString();

        $query = FixedAsset::with(['company'])
            ->where('status', '!=', 'disposed')
            ->where('purchase_date', '<=', $asOfDate);

        // Filter by company
        if (isset($validated['company_id'])) {
            $query->where('company_id', $validated['company_id']);
        }

        $assets = $query->get();

        // Group by category
        $valuationByCategory = $assets->groupBy('asset_category')->map(function ($categoryAssets, $category) {
            return [
                'category' => $category,
                'count' => $categoryAssets->count(),
                'total_cost' => $categoryAssets->sum('purchase_cost'),
                'total_accumulated_depreciation' => $categoryAssets->sum('accumulated_depreciation'),
                'total_book_value' => $categoryAssets->sum(function ($asset) {
                    return $asset->book_value;
                }),
            ];
        })->values();

        $summary = [
            'total_assets' => $assets->count(),
            'total_cost' => $assets->sum('purchase_cost'),
            'total_accumulated_depreciation' => $assets->sum('accumulated_depreciation'),
            'total_book_value' => $assets->sum(function ($asset) {
                return $asset->book_value;
            }),
            'as_of_date' => $asOfDate,
        ];

        return response()->json([
            'valuation_by_category' => $valuationByCategory,
            'summary' => $summary,
        ]);
    }
}
