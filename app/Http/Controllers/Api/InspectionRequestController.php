<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = InspectionRequest::where('company_id', $user->company_id)
            ->with(['project', 'inspectionType', 'requestedBy', 'inspector']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }

        $requests = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'inspection_type_id' => 'required|exists:inspection_types,id',
            'request_date' => 'required|date',
            'requested_date' => 'required|date',
            'requested_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'work_area' => 'nullable|string|max:255',
            'activity_id' => 'nullable|exists:project_activities,id',
            'boq_item_id' => 'nullable|exists:boq_items,id',
            'description' => 'nullable|string',
            'priority' => 'required|in:normal,urgent',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $validated['requested_by_id'] = $user->id;
        $validated['company_id'] = $user->company_id;
        $validated['status'] = 'pending';

        $inspectionRequest = InspectionRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection request created successfully',
            'data' => $inspectionRequest->load(['project', 'inspectionType', 'requestedBy']),
        ], 201);
    }

    public function show(InspectionRequest $inspectionRequest): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $inspectionRequest->load([
                'project', 
                'inspectionType', 
                'requestedBy', 
                'inspector',
                'inspection',
                'activity',
                'boqItem'
            ]),
        ]);
    }

    public function update(Request $request, InspectionRequest $inspectionRequest): JsonResponse
    {
        $validated = $request->validate([
            'requested_date' => 'date',
            'requested_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'work_area' => 'nullable|string|max:255',
            'activity_id' => 'nullable|exists:project_activities,id',
            'boq_item_id' => 'nullable|exists:boq_items,id',
            'description' => 'nullable|string',
            'priority' => 'in:normal,urgent',
            'notes' => 'nullable|string',
        ]);

        $inspectionRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection request updated successfully',
            'data' => $inspectionRequest->load(['project', 'inspectionType', 'requestedBy', 'inspector']),
        ]);
    }

    public function destroy(InspectionRequest $inspectionRequest): JsonResponse
    {
        $inspectionRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inspection request deleted successfully',
        ]);
    }

    public function byProject(Request $request, $projectId): JsonResponse
    {
        $user = Auth::user();
        $query = InspectionRequest::where('company_id', $user->company_id)
            ->where('project_id', $projectId)
            ->with(['inspectionType', 'requestedBy', 'inspector']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function schedule(Request $request, $id): JsonResponse
    {
        $inspectionRequest = InspectionRequest::findOrFail($id);

        $validated = $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'inspector_id' => 'required|exists:users,id',
        ]);

        $inspectionRequest->update([
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'inspector_id' => $validated['inspector_id'],
            'status' => 'scheduled',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection scheduled successfully',
            'data' => $inspectionRequest->load(['project', 'inspectionType', 'requestedBy', 'inspector']),
        ]);
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        $inspectionRequest = InspectionRequest::findOrFail($id);

        $inspectionRequest->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection request cancelled successfully',
            'data' => $inspectionRequest,
        ]);
    }

    public function reject(Request $request, $id): JsonResponse
    {
        $inspectionRequest = InspectionRequest::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $inspectionRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection request rejected',
            'data' => $inspectionRequest,
        ]);
    }
}
