<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionActionController extends Controller
{
    public function byInspection($inspectionId): JsonResponse
    {
        $actions = InspectionAction::where('inspection_id', $inspectionId)
            ->with(['assignedTo', 'verifiedBy', 'inspectionItem'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $actions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'inspection_id' => 'required|exists:inspections,id',
            'inspection_item_id' => 'nullable|exists:inspection_items,id',
            'action_type' => 'required|in:corrective,preventive',
            'description' => 'required|string',
            'assigned_to_id' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';

        $action = InspectionAction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection action created successfully',
            'data' => $action->load(['assignedTo', 'inspection']),
        ], 201);
    }

    public function complete(Request $request, $id): JsonResponse
    {
        $action = InspectionAction::findOrFail($id);

        $validated = $request->validate([
            'completed_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $action->update([
            'status' => 'completed',
            'completed_date' => $validated['completed_date'],
            'remarks' => $validated['remarks'] ?? $action->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Action marked as completed',
            'data' => $action,
        ]);
    }

    public function verify(Request $request, $id): JsonResponse
    {
        $action = InspectionAction::findOrFail($id);
        $user = Auth::user();

        $action->update([
            'status' => 'verified',
            'verification_date' => now(),
            'verified_by_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Action verified successfully',
            'data' => $action->load('verifiedBy'),
        ]);
    }
}
