<?php

namespace App\Http\Controllers;

use App\Models\RiskIncident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskIncidentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RiskIncident::with(['risk', 'project', 'reportedBy']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $incidents = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $incidents,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'risk_id' => 'nullable|exists:risks,id',
            'project_id' => 'required|exists:projects,id',
            'incident_date' => 'required|date',
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'actual_cost_impact' => 'nullable|numeric',
            'actual_schedule_impact' => 'nullable|integer',
            'root_cause' => 'nullable|string',
            'immediate_actions' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        $validated['reported_by_id'] = Auth::id();
        $incident = RiskIncident::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Incident created successfully',
            'data' => $incident,
        ], 201);
    }

    public function show(RiskIncident $riskIncident): JsonResponse
    {
        $riskIncident->load(['risk', 'project', 'reportedBy']);

        return response()->json([
            'success' => true,
            'data' => $riskIncident,
        ]);
    }

    public function update(Request $request, RiskIncident $riskIncident): JsonResponse
    {
        $validated = $request->validate([
            'corrective_actions' => 'nullable|string',
            'preventive_actions' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
            'status' => 'sometimes|in:reported,investigating,resolved,closed',
        ]);

        $riskIncident->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Incident updated successfully',
            'data' => $riskIncident,
        ]);
    }

    public function destroy(RiskIncident $riskIncident): JsonResponse
    {
        $riskIncident->delete();

        return response()->json([
            'success' => true,
            'message' => 'Incident deleted successfully',
        ]);
    }

    public function resolve($id): JsonResponse
    {
        $incident = RiskIncident::findOrFail($id);
        $incident->resolve();

        return response()->json([
            'success' => true,
            'message' => 'Incident resolved successfully',
            'data' => $incident,
        ]);
    }
}
