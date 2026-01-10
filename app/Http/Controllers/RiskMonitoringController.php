<?php

namespace App\Http\Controllers;

use App\Models\RiskMonitoring;
use App\Models\Risk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskMonitoringController extends Controller
{
    public function byRisk($riskId): JsonResponse
    {
        $monitoring = RiskMonitoring::where('risk_id', $riskId)
            ->with('monitoredBy')
            ->latest('monitoring_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $monitoring,
        ]);
    }

    public function store(Request $request, $riskId): JsonResponse
    {
        $risk = Risk::findOrFail($riskId);

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

        return response()->json([
            'success' => true,
            'message' => 'Monitoring record created successfully',
            'data' => $monitoring,
        ], 201);
    }
}
