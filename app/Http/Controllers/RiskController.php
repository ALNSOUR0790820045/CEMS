<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\RiskAssessment;
use App\Models\RiskResponse;
use App\Models\RiskMonitoring;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Risk::with(['riskRegister', 'project', 'owner', 'identifiedBy']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('risk_register_id')) {
            $query->where('risk_register_id', $request->risk_register_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $risks = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $risks,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'risk_register_id' => 'required|exists:risk_registers,id',
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,financial,schedule,safety,environmental,contractual,resource,external',
            'source' => 'nullable|string',
            'trigger_events' => 'nullable|string',
            'affected_objectives' => 'nullable|array',
            'identification_date' => 'required|date',
            'probability' => 'required|in:very_low,low,medium,high,very_high',
            'probability_score' => 'required|integer|min:1|max:5',
            'impact' => 'required|in:very_low,low,medium,high,very_high',
            'impact_score' => 'required|integer|min:1|max:5',
            'cost_impact_min' => 'nullable|numeric',
            'cost_impact_max' => 'nullable|numeric',
            'cost_impact_expected' => 'nullable|numeric',
            'schedule_impact_days' => 'nullable|integer',
            'response_strategy' => 'nullable|in:avoid,mitigate,transfer,accept',
            'response_plan' => 'nullable|string',
            'contingency_plan' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $validated['identified_by_id'] = Auth::id();
        $validated['status'] = 'identified';

        $risk = Risk::create($validated);
        $risk->load(['riskRegister', 'project', 'owner', 'identifiedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Risk created successfully',
            'data' => $risk,
        ], 201);
    }

    public function show(Risk $risk): JsonResponse
    {
        $risk->load(['riskRegister', 'project', 'owner', 'identifiedBy', 'assessments', 'responses', 'monitoring']);

        return response()->json([
            'success' => true,
            'data' => $risk,
        ]);
    }

    public function update(Request $request, Risk $risk): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|in:technical,financial,schedule,safety,environmental,contractual,resource,external',
            'probability' => 'sometimes|in:very_low,low,medium,high,very_high',
            'probability_score' => 'sometimes|integer|min:1|max:5',
            'impact' => 'sometimes|in:very_low,low,medium,high,very_high',
            'impact_score' => 'sometimes|integer|min:1|max:5',
            'response_strategy' => 'nullable|in:avoid,mitigate,transfer,accept',
            'response_plan' => 'nullable|string',
            'status' => 'sometimes|in:identified,analyzing,responding,monitoring,closed,occurred',
        ]);

        $risk->update($validated);
        $risk->load(['riskRegister', 'project', 'owner']);

        return response()->json([
            'success' => true,
            'message' => 'Risk updated successfully',
            'data' => $risk,
        ]);
    }

    public function destroy(Risk $risk): JsonResponse
    {
        $risk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Risk deleted successfully',
        ]);
    }

    public function byProject($projectId): JsonResponse
    {
        $risks = Risk::where('project_id', $projectId)
            ->with(['owner', 'identifiedBy'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $risks,
        ]);
    }

    public function byRegister($registerId): JsonResponse
    {
        $risks = Risk::where('risk_register_id', $registerId)
            ->with(['owner', 'identifiedBy'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $risks,
        ]);
    }

    public function assess(Request $request, $id): JsonResponse
    {
        $risk = Risk::findOrFail($id);

        $validated = $request->validate([
            'assessment_date' => 'required|date',
            'assessment_type' => 'required|in:initial,reassessment,post_response',
            'probability' => 'required|in:very_low,low,medium,high,very_high',
            'probability_score' => 'required|integer|min:1|max:5',
            'impact' => 'required|in:very_low,low,medium,high,very_high',
            'impact_score' => 'required|integer|min:1|max:5',
            'cost_impact' => 'nullable|numeric',
            'schedule_impact' => 'nullable|integer',
            'justification' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $validated['assessed_by_id'] = Auth::id();
        $validated['risk_id'] = $risk->id;

        $assessment = RiskAssessment::create($validated);

        // Update risk with new assessment values
        $risk->update([
            'probability' => $validated['probability'],
            'probability_score' => $validated['probability_score'],
            'impact' => $validated['impact'],
            'impact_score' => $validated['impact_score'],
            'status' => 'analyzing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Risk assessed successfully',
            'data' => $assessment,
        ], 201);
    }

    public function addResponse(Request $request, $id): JsonResponse
    {
        $risk = Risk::findOrFail($id);

        $validated = $request->validate([
            'response_number' => 'required|string',
            'response_type' => 'required|in:preventive,corrective,contingency',
            'strategy' => 'required|in:avoid,mitigate,transfer,accept',
            'description' => 'required|string',
            'action_required' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
            'target_date' => 'nullable|date',
            'cost_of_response' => 'nullable|numeric',
        ]);

        $validated['risk_id'] = $risk->id;
        $response = RiskResponse::create($validated);

        $risk->update(['status' => 'responding']);

        return response()->json([
            'success' => true,
            'message' => 'Risk response added successfully',
            'data' => $response,
        ], 201);
    }

    public function monitor(Request $request, $id): JsonResponse
    {
        $risk = Risk::findOrFail($id);

        $validated = $request->validate([
            'monitoring_date' => 'required|date',
            'current_status' => 'required|string',
            'probability_change' => 'required|in:increased,same,decreased',
            'impact_change' => 'required|in:increased,same,decreased',
            'trigger_status' => 'required|in:not_triggered,warning,triggered',
            'early_warning_signs' => 'nullable|string',
            'actions_taken' => 'nullable|string',
            'effectiveness' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'next_review_date' => 'nullable|date',
        ]);

        $validated['monitored_by_id'] = Auth::id();
        $validated['risk_id'] = $risk->id;

        $monitoring = RiskMonitoring::create($validated);

        $risk->update(['status' => 'monitoring']);

        return response()->json([
            'success' => true,
            'message' => 'Risk monitoring recorded successfully',
            'data' => $monitoring,
        ], 201);
    }

    public function close(Request $request, $id): JsonResponse
    {
        $risk = Risk::findOrFail($id);

        $validated = $request->validate([
            'closure_reason' => 'required|string',
            'lessons_learned' => 'nullable|string',
        ]);

        $risk->close($validated['closure_reason'], $validated['lessons_learned'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Risk closed successfully',
            'data' => $risk,
        ]);
    }

    public function escalate($id): JsonResponse
    {
        $risk = Risk::findOrFail($id);
        $risk->escalate();

        return response()->json([
            'success' => true,
            'message' => 'Risk escalated successfully',
        ]);
    }
}
