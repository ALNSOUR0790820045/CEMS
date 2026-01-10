<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetDisposal;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetDisposalController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetDisposal::with(['fixedAsset', 'approvedBy']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('disposal_type')) {
            $query->where('disposal_type', $request->disposal_type);
        }

        if ($request->has('fixed_asset_id')) {
            $query->where('fixed_asset_id', $request->fixed_asset_id);
        }

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        $disposals = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $disposals
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fixed_asset_id' => 'required|exists:fixed_assets,id',
            'disposal_date' => 'required|date',
            'disposal_type' => 'required|in:sale,write_off,donation,scrap',
            'disposal_reason' => 'nullable|string',
            'sale_price' => 'nullable|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $asset = FixedAsset::findOrFail($validated['fixed_asset_id']);

            // Check if asset is already disposed
            if (in_array($asset->status, ['disposed', 'sold', 'written_off'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset is already disposed'
                ], 422);
            }

            $validated['company_id'] = auth()->user()->company_id;
            $validated['status'] = 'pending';
            $validated['accumulated_depreciation_at_disposal'] = $asset->accumulated_depreciation;
            $validated['net_book_value_at_disposal'] = $asset->net_book_value;

            // Calculate gain/loss if it's a sale
            if ($validated['disposal_type'] === 'sale' && isset($validated['sale_price'])) {
                $validated['gain_loss'] = $validated['sale_price'] - $asset->net_book_value;
            }

            $disposal = AssetDisposal::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset disposal created successfully',
                'data' => $disposal->load(['fixedAsset'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset disposal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $disposal = AssetDisposal::with([
            'fixedAsset.category',
            'fixedAsset.currency',
            'approvedBy',
            'glJournalEntry'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $disposal
        ]);
    }

    public function update(Request $request, $id)
    {
        $disposal = AssetDisposal::findOrFail($id);

        // Only pending disposals can be updated
        if ($disposal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending disposals can be updated'
            ], 422);
        }

        $validated = $request->validate([
            'disposal_date' => 'sometimes|required|date',
            'disposal_type' => 'sometimes|required|in:sale,write_off,donation,scrap',
            'disposal_reason' => 'nullable|string',
            'sale_price' => 'nullable|numeric|min:0',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            // Recalculate gain/loss if sale price changed
            if (isset($validated['sale_price']) && $disposal->disposal_type === 'sale') {
                $validated['gain_loss'] = $validated['sale_price'] - $disposal->net_book_value_at_disposal;
            }

            $disposal->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Asset disposal updated successfully',
                'data' => $disposal->fresh(['fixedAsset'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset disposal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $disposal = AssetDisposal::findOrFail($id);

        // Only pending disposals can be deleted
        if ($disposal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending disposals can be deleted'
            ], 422);
        }

        try {
            $disposal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset disposal deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset disposal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        $disposal = AssetDisposal::findOrFail($id);

        if ($disposal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Disposal is not pending approval'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $disposal->update([
                'status' => 'approved',
                'approved_by_id' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset disposal approved successfully',
                'data' => $disposal->fresh(['fixedAsset', 'approvedBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve asset disposal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        $disposal = AssetDisposal::findOrFail($id);

        if ($disposal->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Disposal must be approved before completion'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update disposal status
            $disposal->update([
                'status' => 'completed',
            ]);

            // Update asset status based on disposal type
            $asset = $disposal->fixedAsset;
            $newStatus = match($disposal->disposal_type) {
                'sale' => 'sold',
                'write_off' => 'written_off',
                default => 'disposed'
            };

            $asset->update(['status' => $newStatus]);

            // TODO: Create GL journal entry for disposal

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset disposal completed successfully',
                'data' => $disposal->fresh(['fixedAsset'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete asset disposal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
