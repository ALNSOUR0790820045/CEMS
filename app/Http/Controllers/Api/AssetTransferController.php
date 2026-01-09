<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetTransfer;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetTransfer::with([
            'fixedAsset',
            'fromLocation',
            'toLocation',
            'fromDepartment',
            'toDepartment',
            'requestedBy',
            'approvedBy'
        ]);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('fixed_asset_id')) {
            $query->where('fixed_asset_id', $request->fixed_asset_id);
        }

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        $transfers = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transfers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fixed_asset_id' => 'required|exists:fixed_assets,id',
            'transfer_date' => 'required|date',
            'from_location_id' => 'nullable|exists:warehouse_locations,id',
            'to_location_id' => 'nullable|exists:warehouse_locations,id',
            'from_department_id' => 'nullable|exists:departments,id',
            'to_department_id' => 'nullable|exists:departments,id',
            'from_project_id' => 'nullable|exists:projects,id',
            'to_project_id' => 'nullable|exists:projects,id',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $asset = FixedAsset::findOrFail($validated['fixed_asset_id']);

            // Check if asset can be transferred
            if (!in_array($asset->status, ['active', 'under_maintenance'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset cannot be transferred in current status'
                ], 422);
            }

            // Set from values from current asset if not provided
            if (!isset($validated['from_location_id'])) {
                $validated['from_location_id'] = $asset->location_id;
            }
            if (!isset($validated['from_department_id'])) {
                $validated['from_department_id'] = $asset->department_id;
            }
            if (!isset($validated['from_project_id'])) {
                $validated['from_project_id'] = $asset->project_id;
            }

            $validated['company_id'] = auth()->user()->company_id;
            $validated['status'] = 'pending';
            $validated['requested_by_id'] = auth()->id();

            $transfer = AssetTransfer::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset transfer created successfully',
                'data' => $transfer->load([
                    'fixedAsset',
                    'fromLocation',
                    'toLocation',
                    'fromDepartment',
                    'toDepartment',
                    'requestedBy'
                ])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $transfer = AssetTransfer::with([
            'fixedAsset.category',
            'fixedAsset.currency',
            'fromLocation',
            'toLocation',
            'fromDepartment',
            'toDepartment',
            'fromProject',
            'toProject',
            'requestedBy',
            'approvedBy'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transfer
        ]);
    }

    public function update(Request $request, $id)
    {
        $transfer = AssetTransfer::findOrFail($id);

        // Only pending transfers can be updated
        if ($transfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending transfers can be updated'
            ], 422);
        }

        $validated = $request->validate([
            'transfer_date' => 'sometimes|required|date',
            'to_location_id' => 'nullable|exists:warehouse_locations,id',
            'to_department_id' => 'nullable|exists:departments,id',
            'to_project_id' => 'nullable|exists:projects,id',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $transfer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Asset transfer updated successfully',
                'data' => $transfer->fresh([
                    'fixedAsset',
                    'toLocation',
                    'toDepartment',
                    'toProject'
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $transfer = AssetTransfer::findOrFail($id);

        // Only pending transfers can be deleted
        if ($transfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending transfers can be deleted'
            ], 422);
        }

        try {
            $transfer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset transfer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        $transfer = AssetTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transfer is not pending approval'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $transfer->update([
                'status' => 'approved',
                'approved_by_id' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset transfer approved successfully',
                'data' => $transfer->fresh([
                    'fixedAsset',
                    'toLocation',
                    'toDepartment',
                    'approvedBy'
                ])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve asset transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        $transfer = AssetTransfer::findOrFail($id);

        if ($transfer->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Transfer must be approved before completion'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update transfer status
            $transfer->update([
                'status' => 'completed',
            ]);

            // Update asset location, department, project
            $asset = $transfer->fixedAsset;
            $updateData = [];

            if ($transfer->to_location_id) {
                $updateData['location_id'] = $transfer->to_location_id;
            }
            if ($transfer->to_department_id) {
                $updateData['department_id'] = $transfer->to_department_id;
            }
            if ($transfer->to_project_id) {
                $updateData['project_id'] = $transfer->to_project_id;
            }

            if (!empty($updateData)) {
                $asset->update($updateData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset transfer completed successfully',
                'data' => $transfer->fresh(['fixedAsset'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete asset transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
