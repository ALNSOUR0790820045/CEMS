<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CostForecastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CostForecast::with([
            'project',
            'budgetItem',
            'costCode',
            'preparedBy'
        ]);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('forecast_type')) {
            $query->where('forecast_type', $request->forecast_type);
        }

        if ($request->has('period_month') && $request->has('period_year')) {
            $query->where('period_month', $request->period_month)
                  ->where('period_year', $request->period_year);
        }

        $forecasts = $query->latest('forecast_date')->paginate($request->per_page ?? 15);

        return response()->json($forecasts);
    }

    /**
     * Get forecasts by project
     */
    public function byProject($projectId)
    {
        $forecasts = CostForecast::with([
            'budgetItem',
            'costCode',
            'preparedBy'
        ])
            ->where('project_id', $projectId)
            ->latest('forecast_date')
            ->get();

        return response()->json($forecasts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'forecast_date' => 'required|date',
            'forecast_type' => 'required|in:monthly,quarterly,completion',
            'period_month' => 'nullable|integer|min:1|max:12',
            'period_year' => 'nullable|integer|min:2000|max:2100',
            'budget_item_id' => 'nullable|exists:project_budget_items,id',
            'cost_code_id' => 'required|exists:cost_codes,id',
            'forecast_amount' => 'required|numeric|min:0',
            'basis' => 'required|in:trend,percentage,manual',
            'assumptions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $forecast = CostForecast::create(array_merge(
            $validator->validated(),
            [
                'prepared_by_id' => $request->user()->id,
            ]
        ));

        return response()->json($forecast->load(['project', 'costCode']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $forecast = CostForecast::with([
            'project',
            'budgetItem',
            'costCode',
            'preparedBy'
        ])->findOrFail($id);

        return response()->json($forecast);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $forecast = CostForecast::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'forecast_date' => 'nullable|date',
            'forecast_amount' => 'nullable|numeric|min:0',
            'basis' => 'nullable|in:trend,percentage,manual',
            'assumptions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $forecast->update($request->all());

        return response()->json($forecast->load(['project', 'costCode']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $forecast = CostForecast::findOrFail($id);
        $forecast->delete();

        return response()->json(['message' => 'Forecast deleted successfully']);
    }

    /**
     * Generate forecasts for a project
     */
    public function generate(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(), [
            'forecast_type' => 'required|in:monthly,quarterly,completion',
            'basis' => 'required|in:trend,percentage,manual',
            'period_month' => 'nullable|integer|min:1|max:12',
            'period_year' => 'nullable|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // This would implement automatic forecast generation based on trends
        // Placeholder for actual implementation
        
        return response()->json(['message' => 'Forecast generation to be implemented']);
    }
}
