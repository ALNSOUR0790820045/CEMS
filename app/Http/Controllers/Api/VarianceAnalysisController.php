<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VarianceAnalysis;
use App\Models\ProjectBudget;
use App\Models\ActualCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VarianceAnalysisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VarianceAnalysis::with([
            'project',
            'costCode',
            'responsiblePerson'
        ]);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('variance_type')) {
            $query->where('variance_type', $request->variance_type);
        }

        if ($request->has('period_month') && $request->has('period_year')) {
            $query->where('period_month', $request->period_month)
                  ->where('period_year', $request->period_year);
        }

        $analyses = $query->latest('analysis_date')->paginate($request->per_page ?? 15);

        return response()->json($analyses);
    }

    /**
     * Get variance analyses by project
     */
    public function byProject($projectId)
    {
        $analyses = VarianceAnalysis::with([
            'costCode',
            'responsiblePerson'
        ])
            ->where('project_id', $projectId)
            ->latest('analysis_date')
            ->get();

        return response()->json($analyses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'analysis_date' => 'required|date',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'cost_code_id' => 'required|exists:cost_codes,id',
            'budgeted_amount' => 'required|numeric',
            'actual_amount' => 'required|numeric',
            'variance_reason' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'responsible_person_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $analysis = new VarianceAnalysis($validator->validated());
        $analysis->calculateVariance();
        $analysis->save();

        return response()->json($analysis->load(['project', 'costCode']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $analysis = VarianceAnalysis::with([
            'project',
            'costCode',
            'responsiblePerson'
        ])->findOrFail($id);

        return response()->json($analysis);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $analysis = VarianceAnalysis::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'budgeted_amount' => 'nullable|numeric',
            'actual_amount' => 'nullable|numeric',
            'variance_reason' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'responsible_person_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:identified,analyzed,action_taken,closed',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $analysis->update($request->all());

        // Recalculate variance if amounts changed
        if ($request->has(['budgeted_amount', 'actual_amount'])) {
            $analysis->calculateVariance();
        }

        return response()->json($analysis->load(['project', 'costCode']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $analysis = VarianceAnalysis::findOrFail($id);
        $analysis->delete();

        return response()->json(['message' => 'Variance analysis deleted successfully']);
    }

    /**
     * Analyze project variances for a period
     */
    public function analyze(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(), [
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $month = $request->period_month;
            $year = $request->period_year;

            // Get active budget for the project
            $budget = ProjectBudget::where('project_id', $projectId)
                ->where('status', 'active')
                ->first();

            if (!$budget) {
                return response()->json(['error' => 'No active budget found for this project'], 404);
            }

            // Get budget items with their cost codes
            $budgetItems = $budget->items()
                ->with('costCode')
                ->get();

            $analyses = [];

            foreach ($budgetItems as $item) {
                // Get actual costs for this cost code in the period
                $startDate = sprintf('%04d-%02d-01', $year, $month);
                $endDate = date('Y-m-t', strtotime($startDate));

                $actualAmount = ActualCost::where('project_id', $projectId)
                    ->where('cost_code_id', $item->cost_code_id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('amount_local');

                // Calculate variance
                $varianceAmount = $item->budgeted_amount - $actualAmount;
                $variancePercentage = $item->budgeted_amount > 0 
                    ? ($varianceAmount / $item->budgeted_amount) * 100 
                    : 0;

                // Get variance threshold from config or use default
                $varianceThreshold = config('cost_control.variance_threshold', 5);
                
                // Only create analysis if there's a significant variance
                if (abs($variancePercentage) > $varianceThreshold) {
                    $analysis = VarianceAnalysis::create([
                        'project_id' => $projectId,
                        'analysis_date' => now(),
                        'period_month' => $month,
                        'period_year' => $year,
                        'cost_code_id' => $item->cost_code_id,
                        'budgeted_amount' => $item->budgeted_amount,
                        'actual_amount' => $actualAmount,
                        'variance_amount' => $varianceAmount,
                        'variance_percentage' => $variancePercentage,
                        'variance_type' => $varianceAmount >= 0 ? 'favorable' : 'unfavorable',
                        'status' => 'identified',
                    ]);

                    $analyses[] = $analysis;
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Variance analysis completed',
                'analyses_created' => count($analyses),
                'data' => $analyses
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to analyze variances: ' . $e->getMessage()], 500);
        }
    }
}
