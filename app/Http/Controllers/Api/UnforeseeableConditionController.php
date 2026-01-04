<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnforeseeableCondition;
use App\Models\UnforeseeableConditionEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UnforeseeableConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UnforeseeableCondition::with([
            'project',
            'contract',
            'reportedBy',
            'verifiedBy',
            'evidence'
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by condition type
        if ($request->has('condition_type')) {
            $query->where('condition_type', $request->condition_type);
        }

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by contract
        if ($request->has('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }

        $conditions = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($conditions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'location_latitude' => 'nullable|numeric|between:-90,90',
            'location_longitude' => 'nullable|numeric|between:-180,180',
            'condition_type' => 'required|in:ground_conditions,rock_conditions,water_conditions,contamination,underground_utilities,archaeological,unexploded_ordnance,other',
            'discovery_date' => 'required|date',
            'notice_date' => 'nullable|date',
            'inspection_date' => 'nullable|date',
            'contractual_clause' => 'nullable|string|max:255',
            'impact_description' => 'required|string',
            'estimated_delay_days' => 'nullable|integer|min:0',
            'estimated_cost_impact' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'tender_assumptions' => 'nullable|string',
            'site_investigation_data' => 'nullable|string',
            'actual_conditions' => 'required|string',
            'difference_analysis' => 'required|string',
            'immediate_measures' => 'nullable|string',
            'proposed_solution' => 'nullable|string',
            'status' => 'nullable|in:identified,notice_sent,under_investigation,agreed,disputed,resolved,rejected',
            'time_bar_event_id' => 'nullable|exists:time_bar_events,id',
            'claim_id' => 'nullable|exists:claims,id',
            'eot_id' => 'nullable|exists:eot_requests,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate condition number
        $year = date('Y');
        $prefix = 'UFC-' . $year . '-';
        $lastCondition = UnforeseeableCondition::where('condition_number', 'like', $prefix . '%')
            ->orderBy('condition_number', 'desc')
            ->first();
        
        $nextNumber = 1;
        if ($lastCondition && preg_match('/UFC-\d{4}-(\d{4})$/', $lastCondition->condition_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        $conditionNumber = sprintf('UFC-%s-%04d', $year, $nextNumber);

        $data = $validator->validated();
        $data['condition_number'] = $conditionNumber;
        $data['reported_by'] = auth()->id();
        $data['contractual_clause'] = $data['contractual_clause'] ?? '4.12';

        $condition = UnforeseeableCondition::create($data);

        return response()->json([
            'message' => 'Unforeseeable condition created successfully',
            'data' => $condition->load(['project', 'contract', 'reportedBy'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $condition = UnforeseeableCondition::with([
            'project',
            'contract',
            'timeBarEvent',
            'claim',
            'eotRequest',
            'reportedBy',
            'verifiedBy',
            'evidence.uploadedBy'
        ])->findOrFail($id);

        return response()->json($condition);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $condition = UnforeseeableCondition::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'project_id' => 'sometimes|required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'location' => 'sometimes|required|string|max:255',
            'location_latitude' => 'nullable|numeric|between:-90,90',
            'location_longitude' => 'nullable|numeric|between:-180,180',
            'condition_type' => 'sometimes|required|in:ground_conditions,rock_conditions,water_conditions,contamination,underground_utilities,archaeological,unexploded_ordnance,other',
            'discovery_date' => 'sometimes|required|date',
            'notice_date' => 'nullable|date',
            'inspection_date' => 'nullable|date',
            'contractual_clause' => 'nullable|string|max:255',
            'impact_description' => 'sometimes|required|string',
            'estimated_delay_days' => 'nullable|integer|min:0',
            'estimated_cost_impact' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'tender_assumptions' => 'nullable|string',
            'site_investigation_data' => 'nullable|string',
            'actual_conditions' => 'sometimes|required|string',
            'difference_analysis' => 'sometimes|required|string',
            'immediate_measures' => 'nullable|string',
            'proposed_solution' => 'nullable|string',
            'status' => 'nullable|in:identified,notice_sent,under_investigation,agreed,disputed,resolved,rejected',
            'time_bar_event_id' => 'nullable|exists:time_bar_events,id',
            'claim_id' => 'nullable|exists:claims,id',
            'eot_id' => 'nullable|exists:eot_requests,id',
            'verified_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $condition->update($validator->validated());

        return response()->json([
            'message' => 'Unforeseeable condition updated successfully',
            'data' => $condition->load(['project', 'contract', 'reportedBy', 'verifiedBy'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $condition = UnforeseeableCondition::findOrFail($id);
        $condition->delete();

        return response()->json([
            'message' => 'Unforeseeable condition deleted successfully'
        ]);
    }

    /**
     * Send notice for the condition
     */
    public function sendNotice(string $id)
    {
        $condition = UnforeseeableCondition::findOrFail($id);

        if ($condition->status === 'notice_sent') {
            return response()->json([
                'message' => 'Notice has already been sent for this condition'
            ], 400);
        }

        $condition->update([
            'status' => 'notice_sent',
            'notice_date' => now()->toDateString()
        ]);

        return response()->json([
            'message' => 'Notice sent successfully',
            'data' => $condition
        ]);
    }

    /**
     * Upload evidence for the condition
     */
    public function uploadEvidence(Request $request, string $id)
    {
        $condition = UnforeseeableCondition::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'evidence_type' => 'required|in:photo,video,soil_test,survey_report,expert_report,witness_statement,correspondence,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:51200', // 50MB max
            'evidence_date' => 'required|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'capture_timestamp' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        
        // Generate secure random filename
        $fileName = Str::random(40) . '.' . $extension;
        $filePath = $file->storeAs('unforeseeable_conditions/' . $condition->id, $fileName, 'public');

        $evidence = UnforeseeableConditionEvidence::create([
            'condition_id' => $condition->id,
            'evidence_type' => $request->evidence_type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $originalName, // Store original name for display

            'evidence_date' => $request->evidence_date,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'capture_timestamp' => $request->capture_timestamp,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Evidence uploaded successfully',
            'data' => $evidence->load('uploadedBy')
        ], 201);
    }

    /**
     * Export condition report
     */
    public function export(string $id)
    {
        $condition = UnforeseeableCondition::with([
            'project',
            'contract',
            'timeBarEvent',
            'claim',
            'eotRequest',
            'reportedBy',
            'verifiedBy',
            'evidence.uploadedBy'
        ])->findOrFail($id);

        // Return JSON export for now
        // This can be enhanced to generate PDF reports
        return response()->json([
            'export_type' => 'json',
            'export_date' => now()->toDateTimeString(),
            'data' => $condition
        ]);
    }
}
