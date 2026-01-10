<?php

namespace App\Http\Controllers;

use App\Models\RiskResponse;
use App\Models\Risk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiskResponseController extends Controller
{
    public function byRisk($riskId): JsonResponse
    {
        $responses = RiskResponse::where('risk_id', $riskId)
            ->with('responsible')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $responses,
        ]);
    }

    public function store(Request $request, $riskId): JsonResponse
    {
        $risk = Risk::findOrFail($riskId);

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

        return response()->json([
            'success' => true,
            'message' => 'Response created successfully',
            'data' => $response,
        ], 201);
    }

    public function complete($id): JsonResponse
    {
        $response = RiskResponse::findOrFail($id);
        $response->complete();

        return response()->json([
            'success' => true,
            'message' => 'Response marked as completed',
            'data' => $response,
        ]);
    }
}
