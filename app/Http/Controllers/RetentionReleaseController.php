<?php

namespace App\Http\Controllers;

use App\Models\RetentionRelease;
use App\Models\Retention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RetentionReleaseController extends Controller
{
    public function index(Request $request)
    {
        $query = RetentionRelease::with(['retention', 'approvedBy', 'company']);

        if ($request->has('retention_id')) {
            $query->where('retention_id', $request->retention_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $releases = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $releases
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'retention_id' => 'required|exists:retentions,id',
            'release_type' => 'required|in:partial,first_moiety,second_moiety,full',
            'release_date' => 'required|date',
            'release_amount' => 'required|numeric|min:0',
            'release_percentage' => 'required|numeric|min:0|max:100',
            'release_condition_met' => 'nullable|string',
            'condition_date' => 'nullable|date',
            'certificate_reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        $retention = Retention::findOrFail($validated['retention_id']);

        // Validate release amount doesn't exceed balance
        if ($validated['release_amount'] > $retention->balance_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Release amount exceeds retention balance'
            ], 422);
        }

        $validated['remaining_balance'] = $retention->balance_amount - $validated['release_amount'];
        $validated['status'] = 'pending';

        $release = RetentionRelease::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Retention release created successfully',
            'data' => $release->load(['retention', 'company'])
        ], 201);
    }

    public function show($id)
    {
        $release = RetentionRelease::with(['retention', 'approvedBy', 'company'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $release
        ]);
    }

    public function update(Request $request, $id)
    {
        $release = RetentionRelease::findOrFail($id);

        $validated = $request->validate([
            'release_date' => 'sometimes|date',
            'release_amount' => 'sometimes|numeric|min:0',
            'payment_reference' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $release->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Release updated successfully',
            'data' => $release
        ]);
    }

    public function destroy($id)
    {
        $release = RetentionRelease::findOrFail($id);
        
        if ($release->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending releases can be deleted'
            ], 422);
        }

        $release->delete();

        return response()->json([
            'success' => true,
            'message' => 'Release deleted successfully'
        ]);
    }

    public function approve(Request $request, $id)
    {
        $release = RetentionRelease::findOrFail($id);

        if ($release->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Release is not in pending status'
            ], 422);
        }

        $release->update([
            'status' => 'approved',
            'approved_by_id' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Release approved successfully',
            'data' => $release->load('approvedBy')
        ]);
    }

    public function release(Request $request, $id)
    {
        $release = RetentionRelease::findOrFail($id);

        if ($release->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Release must be approved before releasing'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $retention = $release->retention;

            // Update retention amounts
            $retention->update([
                'released_amount' => $retention->released_amount + $release->release_amount,
                'balance_amount' => $retention->balance_amount - $release->release_amount,
                'status' => $retention->balance_amount - $release->release_amount <= 0 ? 'fully_released' : 'partially_released',
            ]);

            // Update release status
            $release->update([
                'status' => 'released',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retention released successfully',
                'data' => [
                    'release' => $release,
                    'retention' => $retention->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to release retention: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markPaid(Request $request, $id)
    {
        $release = RetentionRelease::findOrFail($id);

        if ($release->status !== 'released') {
            return response()->json([
                'success' => false,
                'message' => 'Release must be in released status'
            ], 422);
        }

        $validated = $request->validate([
            'payment_reference' => 'required|string',
            'payment_date' => 'required|date',
        ]);

        $release->update([
            'status' => 'paid',
            'payment_reference' => $validated['payment_reference'],
            'payment_date' => $validated['payment_date'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Release marked as paid successfully',
            'data' => $release
        ]);
    }
}
