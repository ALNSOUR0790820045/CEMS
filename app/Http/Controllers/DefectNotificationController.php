<?php

namespace App\Http\Controllers;

use App\Models\DefectNotification;
use App\Models\DefectsLiability;
use Illuminate\Http\Request;

class DefectNotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = DefectNotification::with('defectsLiability');

        if ($request->has('defects_liability_id')) {
            $query->where('defects_liability_id', $request->defects_liability_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        $notifications = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'defects_liability_id' => 'required|exists:defects_liability,id',
            'notification_date' => 'required|date',
            'notified_by' => 'required|string',
            'defect_description' => 'required|string',
            'location' => 'nullable|string',
            'severity' => 'required|in:minor,major,critical',
            'rectification_deadline' => 'nullable|date',
            'photos' => 'nullable|array',
            'remarks' => 'nullable|string',
        ]);

        $notification = DefectNotification::create($validated);

        // Update defects_liability defects_reported count
        $dlp = DefectsLiability::findOrFail($validated['defects_liability_id']);
        $dlp->increment('defects_reported');

        return response()->json([
            'success' => true,
            'message' => 'Defect notification created successfully',
            'data' => $notification->load('defectsLiability')
        ], 201);
    }

    public function show($id)
    {
        $notification = DefectNotification::with('defectsLiability')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $notification
        ]);
    }

    public function update(Request $request, $id)
    {
        $notification = DefectNotification::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:notified,acknowledged,in_progress,rectified,disputed',
            'rectification_date' => 'nullable|date',
            'cost_to_rectify' => 'nullable|numeric|min:0',
            'deducted_from_retention' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $notification->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Defect notification updated successfully',
            'data' => $notification
        ]);
    }

    public function destroy($id)
    {
        $notification = DefectNotification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Defect notification deleted successfully'
        ]);
    }

    public function acknowledge(Request $request, $id)
    {
        $notification = DefectNotification::findOrFail($id);

        if ($notification->status !== 'notified') {
            return response()->json([
                'success' => false,
                'message' => 'Notification is not in notified status'
            ], 422);
        }

        $notification->update([
            'status' => 'acknowledged',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Defect notification acknowledged',
            'data' => $notification
        ]);
    }

    public function rectify(Request $request, $id)
    {
        $notification = DefectNotification::findOrFail($id);

        if (!in_array($notification->status, ['acknowledged', 'in_progress'])) {
            return response()->json([
                'success' => false,
                'message' => 'Notification must be acknowledged or in progress'
            ], 422);
        }

        $validated = $request->validate([
            'rectification_date' => 'required|date',
            'cost_to_rectify' => 'nullable|numeric|min:0',
            'deducted_from_retention' => 'nullable|numeric|min:0',
        ]);

        $notification->update([
            'status' => 'rectified',
            'rectification_date' => $validated['rectification_date'],
            'cost_to_rectify' => $validated['cost_to_rectify'] ?? null,
            'deducted_from_retention' => $validated['deducted_from_retention'] ?? 0,
        ]);

        // Update defects_liability defects_rectified count
        $dlp = $notification->defectsLiability;
        $dlp->increment('defects_rectified');

        return response()->json([
            'success' => true,
            'message' => 'Defect marked as rectified',
            'data' => $notification
        ]);
    }
}
