<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Models\InspectionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Inspection::where('company_id', $user->company_id)
            ->with(['project', 'inspectionType', 'inspector', 'witness']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('result')) {
            $query->where('result', $request->result);
        }

        if ($request->has('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }

        $inspections = $query->latest('inspection_date')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $inspections,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'inspection_request_id' => 'nullable|exists:inspection_requests,id',
            'project_id' => 'required|exists:projects,id',
            'inspection_type_id' => 'required|exists:inspection_types,id',
            'inspection_date' => 'required|date',
            'inspection_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'work_area' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'inspector_id' => 'required|exists:users,id',
            'witness_id' => 'nullable|exists:users,id',
            'contractor_rep' => 'nullable|string|max:255',
            'consultant_rep' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $user->company_id;
        $validated['status'] = 'draft';

        $inspection = Inspection::create($validated);

        // Update inspection request status if linked
        if ($validated['inspection_request_id']) {
            InspectionRequest::find($validated['inspection_request_id'])->update([
                'status' => 'completed'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inspection created successfully',
            'data' => $inspection->load(['project', 'inspectionType', 'inspector', 'witness']),
        ], 201);
    }

    public function show(Inspection $inspection): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $inspection->load([
                'project', 
                'inspectionType', 
                'inspector', 
                'witness',
                'approvedBy',
                'items',
                'actions',
                'photos',
                'reinspectionOf',
                'reinspections'
            ]),
        ]);
    }

    public function update(Request $request, Inspection $inspection): JsonResponse
    {
        $validated = $request->validate([
            'inspection_date' => 'date',
            'inspection_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'work_area' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'witness_id' => 'nullable|exists:users,id',
            'contractor_rep' => 'nullable|string|max:255',
            'consultant_rep' => 'nullable|string|max:255',
            'result' => 'nullable|in:pass,fail,conditional,not_applicable',
            'overall_score' => 'nullable|numeric|min:0|max:100',
            'defects_found' => 'nullable|integer|min:0',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $inspection->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection updated successfully',
            'data' => $inspection->load(['project', 'inspectionType', 'inspector', 'witness']),
        ]);
    }

    public function destroy(Inspection $inspection): JsonResponse
    {
        $inspection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inspection deleted successfully',
        ]);
    }

    public function byProject(Request $request, $projectId): JsonResponse
    {
        $user = Auth::user();
        $query = Inspection::where('company_id', $user->company_id)
            ->where('project_id', $projectId)
            ->with(['inspectionType', 'inspector', 'witness']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('result')) {
            $query->where('result', $request->result);
        }

        $inspections = $query->latest('inspection_date')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $inspections,
        ]);
    }

    public function getItems($id): JsonResponse
    {
        $inspection = Inspection::findOrFail($id);
        $items = $inspection->items()->with('photos')->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function saveItems(Request $request, $id): JsonResponse
    {
        $inspection = Inspection::findOrFail($id);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.checklist_item_id' => 'nullable|exists:template_items,id',
            'items.*.item_description' => 'required|string',
            'items.*.acceptance_criteria' => 'nullable|string',
            'items.*.result' => 'nullable|in:pass,fail,na',
            'items.*.score' => 'nullable|numeric',
            'items.*.actual_value' => 'nullable|string',
            'items.*.remarks' => 'nullable|string',
            'items.*.requires_action' => 'boolean',
        ]);

        DB::transaction(function () use ($inspection, $validated) {
            // Delete existing items
            $inspection->items()->delete();

            // Create new items
            foreach ($validated['items'] as $itemData) {
                $itemData['inspection_id'] = $inspection->id;
                InspectionItem::create($itemData);
            }

            // Calculate overall score
            $totalScore = $inspection->items()->sum('score');
            $itemsCount = $inspection->items()->count();
            $overallScore = $itemsCount > 0 ? $totalScore / $itemsCount : 0;

            // Count defects
            $defects = $inspection->items()->where('result', 'fail')->count();

            // Determine result
            $passCount = $inspection->items()->where('result', 'pass')->count();
            $failCount = $defects;
            $naCount = $inspection->items()->where('result', 'na')->count();

            $result = 'not_applicable';
            if ($failCount > 0) {
                $result = 'fail';
            } elseif ($passCount > 0) {
                $result = 'pass';
            }

            $inspection->update([
                'overall_score' => round($overallScore, 2),
                'defects_found' => $defects,
                'result' => $result,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Inspection items saved successfully',
            'data' => $inspection->load('items'),
        ]);
    }

    public function submit($id): JsonResponse
    {
        $inspection = Inspection::findOrFail($id);

        if ($inspection->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft inspections can be submitted',
            ], 400);
        }

        $inspection->update(['status' => 'submitted']);

        return response()->json([
            'success' => true,
            'message' => 'Inspection submitted successfully',
            'data' => $inspection,
        ]);
    }

    public function approve(Request $request, $id): JsonResponse
    {
        $inspection = Inspection::findOrFail($id);
        $user = Auth::user();

        if ($inspection->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Only submitted inspections can be approved',
            ], 400);
        }

        $inspection->update([
            'status' => 'approved',
            'approved_by_id' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection approved successfully',
            'data' => $inspection->load('approvedBy'),
        ]);
    }

    public function reject(Request $request, $id): JsonResponse
    {
        $inspection = Inspection::findOrFail($id);

        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);

        $inspection->update([
            'status' => 'rejected',
            'remarks' => $validated['remarks'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection rejected',
            'data' => $inspection,
        ]);
    }

    public function createReinspection(Request $request, $id): JsonResponse
    {
        $originalInspection = Inspection::findOrFail($id);

        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'inspection_time' => 'nullable|date_format:H:i',
            'inspector_id' => 'required|exists:users,id',
        ]);

        $reinspection = Inspection::create([
            'project_id' => $originalInspection->project_id,
            'inspection_type_id' => $originalInspection->inspection_type_id,
            'inspection_date' => $validated['inspection_date'],
            'inspection_time' => $validated['inspection_time'] ?? null,
            'location' => $originalInspection->location,
            'work_area' => $originalInspection->work_area,
            'inspector_id' => $validated['inspector_id'],
            'witness_id' => $originalInspection->witness_id,
            'contractor_rep' => $originalInspection->contractor_rep,
            'consultant_rep' => $originalInspection->consultant_rep,
            'reinspection_of_id' => $originalInspection->id,
            'company_id' => $originalInspection->company_id,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reinspection created successfully',
            'data' => $reinspection->load(['project', 'inspectionType', 'inspector', 'reinspectionOf']),
        ], 201);
    }
}
