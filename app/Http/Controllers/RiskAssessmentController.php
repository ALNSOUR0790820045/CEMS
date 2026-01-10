<?php

namespace App\Http\Controllers;

use App\Models\RiskAssessment;
use App\Models\Risk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskAssessmentController extends Controller
{
    public function byRisk($riskId): JsonResponse
    {
        $assessments = RiskAssessment::where('risk_id', $riskId)
            ->with('assessedBy')
            ->latest('assessment_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assessments,
        ]);
    }

    public function store(Request $request, $riskId): JsonResponse
    {
        $risk = Risk::findOrFail($riskId);

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

        return response()->json([
            'success' => true,
            'message' => 'Assessment created successfully',
            'data' => $assessment,
        ], 201);
    }
}
