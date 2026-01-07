<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KpiDefinition;
use App\Models\KpiValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KpiController extends Controller
{
    /**
     * Display a listing of KPI definitions.
     */
    public function index(Request $request)
    {
        $query = KpiDefinition::with(['company', 'values'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $kpis = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($kpis);
    }

    /**
     * Store a newly created KPI definition.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:kpi_definitions,code|max:255',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:financial,operational,hr,project',
            'calculation_formula' => 'nullable|string',
            'unit' => 'required|in:percentage,currency,number,days',
            'target_value' => 'nullable|numeric',
            'warning_threshold' => 'nullable|numeric',
            'critical_threshold' => 'nullable|numeric',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kpi = KpiDefinition::create([
            'code' => $request->code,
            'name' => $request->name,
            'name_en' => $request->name_en,
            'description' => $request->description,
            'category' => $request->category,
            'calculation_formula' => $request->calculation_formula,
            'unit' => $request->unit,
            'target_value' => $request->target_value,
            'warning_threshold' => $request->warning_threshold,
            'critical_threshold' => $request->critical_threshold,
            'frequency' => $request->frequency,
            'is_active' => $request->is_active ?? true,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json([
            'message' => 'KPI definition created successfully',
            'kpi' => $kpi
        ], 201);
    }

    /**
     * Display the specified KPI definition.
     */
    public function show(Request $request, KpiDefinition $kpiDefinition)
    {
        // Check if KPI belongs to user's company
        if ($kpiDefinition->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $kpiDefinition->load(['company', 'values']);
        return response()->json($kpiDefinition);
    }

    /**
     * Update the specified KPI definition.
     */
    public function update(Request $request, KpiDefinition $kpiDefinition)
    {
        // Check if KPI belongs to user's company
        if ($kpiDefinition->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|max:255|unique:kpi_definitions,code,' . $kpiDefinition->id,
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|in:financial,operational,hr,project',
            'calculation_formula' => 'nullable|string',
            'unit' => 'sometimes|required|in:percentage,currency,number,days',
            'target_value' => 'nullable|numeric',
            'warning_threshold' => 'nullable|numeric',
            'critical_threshold' => 'nullable|numeric',
            'frequency' => 'sometimes|required|in:daily,weekly,monthly,quarterly',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kpiDefinition->update($request->only([
            'code',
            'name',
            'name_en',
            'description',
            'category',
            'calculation_formula',
            'unit',
            'target_value',
            'warning_threshold',
            'critical_threshold',
            'frequency',
            'is_active',
        ]));

        return response()->json([
            'message' => 'KPI definition updated successfully',
            'kpi' => $kpiDefinition
        ]);
    }

    /**
     * Remove the specified KPI definition.
     */
    public function destroy(Request $request, KpiDefinition $kpiDefinition)
    {
        // Check if KPI belongs to user's company
        if ($kpiDefinition->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $kpiDefinition->delete();
        return response()->json(['message' => 'KPI definition deleted successfully']);
    }

    /**
     * Get KPI values.
     */
    public function getValues(Request $request)
    {
        $query = KpiValue::with(['kpiDefinition', 'project', 'department', 'company'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('kpi_definition_id')) {
            $query->where('kpi_definition_id', $request->kpi_definition_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('period_date', [$request->from_date, $request->to_date]);
        }

        $values = $query->latest('period_date')->paginate($request->per_page ?? 15);

        return response()->json($values);
    }

    /**
     * Calculate KPI values.
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kpi_definition_id' => 'required|exists:kpi_definitions,id',
            'period_date' => 'required|date',
            'actual_value' => 'required|numeric',
            'project_id' => 'nullable|exists:projects,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kpiDefinition = KpiDefinition::findOrFail($request->kpi_definition_id);

        // Calculate variance
        $targetValue = $kpiDefinition->target_value ?? 0;
        $actualValue = $request->actual_value;
        $variance = $actualValue - $targetValue;
        $variancePercentage = $targetValue != 0 ? ($variance / $targetValue) * 100 : 0;

        // Determine status
        $status = 'on_track';
        if ($kpiDefinition->critical_threshold && abs($variance) >= $kpiDefinition->critical_threshold) {
            $status = 'critical';
        } elseif ($kpiDefinition->warning_threshold && abs($variance) >= $kpiDefinition->warning_threshold) {
            $status = 'warning';
        }

        // Create or update KPI value
        $kpiValue = KpiValue::updateOrCreate(
            [
                'kpi_definition_id' => $request->kpi_definition_id,
                'period_date' => $request->period_date,
                'project_id' => $request->project_id,
                'department_id' => $request->department_id,
                'company_id' => $request->user()->company_id,
            ],
            [
                'actual_value' => $actualValue,
                'target_value' => $targetValue,
                'variance' => $variance,
                'variance_percentage' => $variancePercentage,
                'status' => $status,
            ]
        );

        return response()->json([
            'message' => 'KPI value calculated successfully',
            'kpi_value' => $kpiValue->load('kpiDefinition')
        ], 201);
    }
}
