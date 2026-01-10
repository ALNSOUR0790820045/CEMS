<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetRevaluation;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetRevaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetRevaluation::with(['fixedAsset', 'approvedBy']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('fixed_asset_id')) {
            $query->where('fixed_asset_id', $request->fixed_asset_id);
        }

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        $revaluations = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $revaluations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fixed_asset_id' => 'required|exists:fixed_assets,id',
            'revaluation_date' => 'required|date',
            'new_value' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
            'appraiser_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $asset = FixedAsset::findOrFail($validated['fixed_asset_id']);

            // Check if asset is active
            if ($asset->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only active assets can be revalued'
                ], 422);
            }

            $validated['company_id'] = auth()->user()->company_id;
            $validated['status'] = 'pending';
            $validated['old_value'] = $asset->net_book_value;

            $revaluation = AssetRevaluation::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset revaluation created successfully',
                'data' => $revaluation->load(['fixedAsset'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset revaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $revaluation = AssetRevaluation::with([
            'fixedAsset.category',
            'fixedAsset.currency',
            'approvedBy',
            'glJournalEntry'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $revaluation
        ]);
    }

    public function update(Request $request, $id)
    {
        $revaluation = AssetRevaluation::findOrFail($id);

        // Only pending revaluations can be updated
        if ($revaluation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending revaluations can be updated'
            ], 422);
        }

        $validated = $request->validate([
            'revaluation_date' => 'sometimes|required|date',
            'new_value' => 'sometimes|required|numeric|min:0',
            'reason' => 'nullable|string',
            'appraiser_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Recalculate surplus/deficit if new value changed
            if (isset($validated['new_value'])) {
                $validated['revaluation_surplus_deficit'] = $validated['new_value'] - $revaluation->old_value;
            }

            $revaluation->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset revaluation updated successfully',
                'data' => $revaluation->fresh(['fixedAsset'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset revaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $revaluation = AssetRevaluation::findOrFail($id);

        // Only pending revaluations can be deleted
        if ($revaluation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending revaluations can be deleted'
            ], 422);
        }

        try {
            $revaluation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset revaluation deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset revaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        $revaluation = AssetRevaluation::findOrFail($id);

        if ($revaluation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Revaluation is not pending approval'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $revaluation->update([
                'status' => 'approved',
                'approved_by_id' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset revaluation approved successfully',
                'data' => $revaluation->fresh(['fixedAsset', 'approvedBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve asset revaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function post(Request $request, $id)
    {
        $revaluation = AssetRevaluation::findOrFail($id);

        if ($revaluation->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Revaluation must be approved before posting'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update revaluation status
            $revaluation->update([
                'status' => 'posted',
            ]);

            // Update asset values
            $asset = $revaluation->fixedAsset;
            $valueDifference = $revaluation->new_value - $revaluation->old_value;

            $asset->update([
                'acquisition_cost' => $asset->acquisition_cost + $valueDifference,
                'net_book_value' => $revaluation->new_value,
            ]);

            // TODO: Create GL journal entry for revaluation

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset revaluation posted successfully',
                'data' => $revaluation->fresh(['fixedAsset'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to post asset revaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
