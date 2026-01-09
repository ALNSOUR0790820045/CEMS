<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use App\Models\AssetDisposal;
use App\Models\AssetMaintenance;
use App\Models\AssetTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetReportController extends Controller
{
    public function assetRegister(Request $request)
    {
        $query = FixedAsset::with(['category', 'currency', 'location', 'department']);

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

        $query->where('company_id', auth()->user()->company_id);

        $assets = $query->orderBy('asset_code')->get();

        // Calculate totals
        $totals = [
            'total_assets' => $assets->count(),
            'total_acquisition_cost' => $assets->sum('acquisition_cost'),
            'total_accumulated_depreciation' => $assets->sum('accumulated_depreciation'),
            'total_net_book_value' => $assets->sum('net_book_value'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'assets' => $assets,
                'totals' => $totals
            ]
        ]);
    }

    public function depreciationSchedule(Request $request)
    {
        $validated = $request->validate([
            'from_month' => 'nullable|integer|min:1|max:12',
            'from_year' => 'nullable|integer|min:2000',
            'to_month' => 'nullable|integer|min:1|max:12',
            'to_year' => 'nullable|integer|min:2000',
            'fixed_asset_id' => 'nullable|exists:fixed_assets,id',
        ]);

        $query = AssetDepreciation::with(['fixedAsset.category']);

        // Apply filters
        if (isset($validated['fixed_asset_id'])) {
            $query->where('fixed_asset_id', $validated['fixed_asset_id']);
        }

        if (isset($validated['from_year'])) {
            $fromMonth = $validated['from_month'] ?? 1;
            $query->where(function ($q) use ($validated, $fromMonth) {
                $q->where('period_year', '>', $validated['from_year'])
                  ->orWhere(function ($q2) use ($validated, $fromMonth) {
                      $q2->where('period_year', $validated['from_year'])
                         ->where('period_month', '>=', $fromMonth);
                  });
            });
        }

        if (isset($validated['to_year'])) {
            $toMonth = $validated['to_month'] ?? 12;
            $query->where(function ($q) use ($validated, $toMonth) {
                $q->where('period_year', '<', $validated['to_year'])
                  ->orWhere(function ($q2) use ($validated, $toMonth) {
                      $q2->where('period_year', $validated['to_year'])
                         ->where('period_month', '<=', $toMonth);
                  });
            });
        }

        $query->where('company_id', auth()->user()->company_id);

        $depreciations = $query->orderBy('period_year')
                               ->orderBy('period_month')
                               ->get();

        // Group by period
        $grouped = $depreciations->groupBy(function ($item) {
            return $item->period_year . '-' . str_pad($item->period_month, 2, '0', STR_PAD_LEFT);
        });

        $schedule = [];
        foreach ($grouped as $period => $items) {
            $schedule[] = [
                'period' => $period,
                'depreciation_amount' => $items->sum('depreciation_amount'),
                'assets_count' => $items->count(),
                'items' => $items
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'schedule' => $schedule,
                'total_depreciation' => $depreciations->sum('depreciation_amount')
            ]
        ]);
    }

    public function assetValuation(Request $request)
    {
        $query = FixedAsset::with(['category', 'currency']);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $query->where('company_id', auth()->user()->company_id)
              ->where('status', 'active');

        $assets = $query->get();

        // Group by category
        $byCategory = $assets->groupBy('category_id')->map(function ($items, $categoryId) {
            $category = $items->first()->category;
            return [
                'category_id' => $categoryId,
                'category_name' => $category->name ?? 'Uncategorized',
                'assets_count' => $items->count(),
                'total_acquisition_cost' => $items->sum('acquisition_cost'),
                'total_accumulated_depreciation' => $items->sum('accumulated_depreciation'),
                'total_net_book_value' => $items->sum('net_book_value'),
            ];
        })->values();

        // Overall totals
        $totals = [
            'total_assets' => $assets->count(),
            'total_acquisition_cost' => $assets->sum('acquisition_cost'),
            'total_accumulated_depreciation' => $assets->sum('accumulated_depreciation'),
            'total_net_book_value' => $assets->sum('net_book_value'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'by_category' => $byCategory,
                'totals' => $totals
            ]
        ]);
    }

    public function assetMovement(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'fixed_asset_id' => 'nullable|exists:fixed_assets,id',
        ]);

        $query = AssetTransfer::with([
            'fixedAsset',
            'fromLocation',
            'toLocation',
            'fromDepartment',
            'toDepartment',
            'requestedBy'
        ])->where('status', 'completed');

        // Apply filters
        if (isset($validated['fixed_asset_id'])) {
            $query->where('fixed_asset_id', $validated['fixed_asset_id']);
        }

        if (isset($validated['from_date'])) {
            $query->where('transfer_date', '>=', $validated['from_date']);
        }

        if (isset($validated['to_date'])) {
            $query->where('transfer_date', '<=', $validated['to_date']);
        }

        $query->where('company_id', auth()->user()->company_id);

        $transfers = $query->orderBy('transfer_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'transfers' => $transfers,
                'total_transfers' => $transfers->count()
            ]
        ]);
    }

    public function maintenanceSchedule(Request $request)
    {
        $query = AssetMaintenance::with(['fixedAsset', 'vendor'])
            ->whereIn('status', ['scheduled', 'in_progress']);

        // Apply filters
        if ($request->has('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        if ($request->has('fixed_asset_id')) {
            $query->where('fixed_asset_id', $request->fixed_asset_id);
        }

        // Filter by upcoming days
        $days = $request->input('days', 90);
        $query->where(function ($q) use ($days) {
            $q->whereNull('next_maintenance_date')
              ->orWhere('next_maintenance_date', '<=', now()->addDays($days));
        });

        $query->where('company_id', auth()->user()->company_id);

        $maintenances = $query->orderBy('next_maintenance_date')->get();

        // Group by status
        $byStatus = $maintenances->groupBy('status')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total_cost' => $items->sum('cost')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'maintenances' => $maintenances,
                'by_status' => $byStatus,
                'total_cost' => $maintenances->sum('cost')
            ]
        ]);
    }

    public function disposalReport(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'disposal_type' => 'nullable|in:sale,write_off,donation,scrap',
            'status' => 'nullable|in:pending,approved,completed',
        ]);

        $query = AssetDisposal::with(['fixedAsset.category', 'approvedBy']);

        // Apply filters
        if (isset($validated['disposal_type'])) {
            $query->where('disposal_type', $validated['disposal_type']);
        }

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['from_date'])) {
            $query->where('disposal_date', '>=', $validated['from_date']);
        }

        if (isset($validated['to_date'])) {
            $query->where('disposal_date', '<=', $validated['to_date']);
        }

        $query->where('company_id', auth()->user()->company_id);

        $disposals = $query->orderBy('disposal_date', 'desc')->get();

        // Group by type
        $byType = $disposals->groupBy('disposal_type')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total_sale_price' => $items->sum('sale_price'),
                'total_net_book_value' => $items->sum('net_book_value_at_disposal'),
                'total_gain_loss' => $items->sum('gain_loss'),
            ];
        });

        // Totals
        $totals = [
            'total_disposals' => $disposals->count(),
            'total_sale_price' => $disposals->sum('sale_price'),
            'total_net_book_value' => $disposals->sum('net_book_value_at_disposal'),
            'total_gain_loss' => $disposals->sum('gain_loss'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'disposals' => $disposals,
                'by_type' => $byType,
                'totals' => $totals
            ]
        ]);
    }
}
