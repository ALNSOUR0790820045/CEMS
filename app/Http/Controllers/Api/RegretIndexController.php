<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialRegretAnalysis;
use App\Models\RegretIndexScenario;
use App\Models\Contract;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class RegretIndexController extends Controller
{
    /**
     * Display a listing of analyses.
     */
    public function index(Request $request)
    {
        $query = FinancialRegretAnalysis::with(['project', 'contract', 'preparedBy', 'reviewedBy']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by contract
        if ($request->has('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('analysis_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('analysis_date', '<=', $request->to_date);
        }

        $analyses = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($analyses);
    }

    /**
     * Calculate and store a new regret index analysis.
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'required|exists:contracts,id',
            'analysis_date' => 'required|date',
            
            // Current project status
            'work_completed_value' => 'required|numeric|min:0',
            'work_completed_percentage' => 'required|numeric|min:0|max:100',
            'elapsed_days' => 'required|integer|min:0',
            
            // Continuation costs
            'continuation_remaining_cost' => 'required|numeric|min:0',
            'continuation_claims_estimate' => 'nullable|numeric|min:0',
            'continuation_variations' => 'nullable|numeric|min:0',
            
            // Termination costs
            'termination_payment_due' => 'required|numeric|min:0',
            'termination_demobilization' => 'nullable|numeric|min:0',
            'termination_claims' => 'nullable|numeric|min:0',
            'termination_legal_costs' => 'nullable|numeric|min:0',
            
            // New contractor costs
            'new_contractor_mobilization' => 'required|numeric|min:0',
            'new_contractor_learning_curve' => 'required|numeric|min:0',
            'new_contractor_premium' => 'required|numeric|min:0',
            'new_contractor_remaining_work' => 'required|numeric|min:0',
            
            // Delay costs
            'estimated_delay_days' => 'required|integer|min:0',
            'delay_cost_per_day' => 'required|numeric|min:0',
            
            // Optional fields
            'analysis_notes' => 'nullable|string',
            'negotiation_points' => 'nullable|string',
            'reviewed_by' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get contract details
        $contract = Contract::findOrFail($request->contract_id);
        
        // Create analysis
        $analysis = new FinancialRegretAnalysis();
        $analysis->analysis_number = FinancialRegretAnalysis::generateAnalysisNumber();
        $analysis->project_id = $request->project_id;
        $analysis->contract_id = $request->contract_id;
        $analysis->analysis_date = $request->analysis_date;
        
        // Contract details
        $analysis->contract_value = $contract->contract_value;
        $analysis->original_duration_days = $contract->duration_days;
        $analysis->currency = $contract->currency;
        
        // Current status
        $analysis->work_completed_value = $request->work_completed_value;
        $analysis->work_completed_percentage = $request->work_completed_percentage;
        $analysis->remaining_work_value = $contract->contract_value - $request->work_completed_value;
        $analysis->elapsed_days = $request->elapsed_days;
        $analysis->remaining_days = $contract->duration_days - $request->elapsed_days;
        
        // Continuation costs
        $analysis->continuation_remaining_cost = $request->continuation_remaining_cost;
        $analysis->continuation_claims_estimate = $request->continuation_claims_estimate ?? 0;
        $analysis->continuation_variations = $request->continuation_variations ?? 0;
        
        // Termination costs
        $analysis->termination_payment_due = $request->termination_payment_due;
        $analysis->termination_demobilization = $request->termination_demobilization ?? 0;
        $analysis->termination_claims = $request->termination_claims ?? 0;
        $analysis->termination_legal_costs = $request->termination_legal_costs ?? 0;
        
        // New contractor costs
        $analysis->new_contractor_mobilization = $request->new_contractor_mobilization;
        $analysis->new_contractor_learning_curve = $request->new_contractor_learning_curve;
        $analysis->new_contractor_premium = $request->new_contractor_premium;
        $analysis->new_contractor_remaining_work = $request->new_contractor_remaining_work;
        
        // Delay costs
        $analysis->estimated_delay_days = $request->estimated_delay_days;
        $analysis->delay_cost_per_day = $request->delay_cost_per_day;
        
        // Additional info
        $analysis->analysis_notes = $request->analysis_notes;
        $analysis->negotiation_points = $request->negotiation_points;
        $analysis->prepared_by = auth()->id();
        $analysis->reviewed_by = $request->reviewed_by;
        
        // Calculate all values
        $analysis->calculateRegretIndex();
        
        $analysis->save();

        return response()->json([
            'message' => 'تم حساب مؤشر الندم المالي بنجاح',
            'data' => $analysis->load(['project', 'contract', 'preparedBy', 'reviewedBy'])
        ], 201);
    }

    /**
     * Display the specified analysis.
     */
    public function show($id)
    {
        $analysis = FinancialRegretAnalysis::with([
            'project', 
            'contract', 
            'preparedBy', 
            'reviewedBy',
            'scenarios'
        ])->findOrFail($id);

        return response()->json($analysis);
    }

    /**
     * Add a scenario to an analysis.
     */
    public function addScenario(Request $request, $id)
    {
        $analysis = FinancialRegretAnalysis::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'scenario_name' => 'required|string|max:255',
            'scenario_type' => 'required|in:optimistic,realistic,pessimistic',
            'assumptions' => 'required|array',
            'assumptions.continuation_cost_multiplier' => 'nullable|numeric|min:0',
            'assumptions.continuation_claims_multiplier' => 'nullable|numeric|min:0',
            'assumptions.continuation_variations_multiplier' => 'nullable|numeric|min:0',
            'assumptions.termination_payment_multiplier' => 'nullable|numeric|min:0',
            'assumptions.termination_claims_multiplier' => 'nullable|numeric|min:0',
            'assumptions.termination_legal_multiplier' => 'nullable|numeric|min:0',
            'assumptions.new_contractor_work_multiplier' => 'nullable|numeric|min:0',
            'assumptions.delay_days_multiplier' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $scenario = new RegretIndexScenario();
        $scenario->analysis_id = $analysis->id;
        $scenario->scenario_name = $request->scenario_name;
        $scenario->scenario_type = $request->scenario_type;
        $scenario->assumptions = $request->assumptions;
        
        // Calculate scenario regret index
        $scenario->calculateScenarioRegretIndex($analysis);
        $scenario->save();

        return response()->json([
            'message' => 'تم إضافة السيناريو بنجاح',
            'data' => $scenario
        ], 201);
    }

    /**
     * Export analysis as PDF.
     */
    public function export($id)
    {
        $analysis = FinancialRegretAnalysis::with([
            'project', 
            'contract', 
            'preparedBy', 
            'reviewedBy',
            'scenarios'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.regret-index-report', compact('analysis'));
        
        return $pdf->download('regret-index-' . $analysis->analysis_number . '.pdf');
    }

    /**
     * Generate presentation data for client.
     */
    public function presentation($id)
    {
        $analysis = FinancialRegretAnalysis::with([
            'project', 
            'contract', 
            'preparedBy', 
            'reviewedBy',
            'scenarios'
        ])->findOrFail($id);

        // Prepare presentation data
        $presentationData = [
            'analysis' => $analysis,
            'summary' => [
                'contract_value' => number_format($analysis->contract_value, 2),
                'work_completed' => number_format($analysis->work_completed_percentage, 1) . '%',
                'cost_to_continue' => number_format($analysis->cost_to_continue, 2),
                'cost_to_terminate' => number_format($analysis->cost_to_terminate, 2),
                'regret_index' => number_format($analysis->regret_index, 2),
                'regret_percentage' => number_format($analysis->regret_percentage, 1) . '%',
                'recommendation' => $this->getRecommendationText($analysis->recommendation),
            ],
            'cost_breakdown' => [
                'continuation' => [
                    'remaining_cost' => $analysis->continuation_remaining_cost,
                    'claims_estimate' => $analysis->continuation_claims_estimate,
                    'variations' => $analysis->continuation_variations,
                    'total' => $analysis->continuation_total,
                ],
                'termination' => [
                    'payment_due' => $analysis->termination_payment_due,
                    'demobilization' => $analysis->termination_demobilization,
                    'claims' => $analysis->termination_claims,
                    'legal_costs' => $analysis->termination_legal_costs,
                    'total' => $analysis->termination_total,
                ],
                'new_contractor' => [
                    'mobilization' => $analysis->new_contractor_mobilization,
                    'learning_curve' => $analysis->new_contractor_learning_curve,
                    'premium' => $analysis->new_contractor_premium,
                    'remaining_work' => $analysis->new_contractor_remaining_work,
                    'total' => $analysis->new_contractor_total,
                ],
                'delay' => [
                    'estimated_days' => $analysis->estimated_delay_days,
                    'cost_per_day' => $analysis->delay_cost_per_day,
                    'total' => $analysis->total_delay_cost,
                ],
            ],
            'scenarios' => $analysis->scenarios->map(function ($scenario) {
                return [
                    'name' => $scenario->scenario_name,
                    'type' => $scenario->scenario_type,
                    'regret_index' => number_format($scenario->regret_index, 2),
                    'assumptions' => $scenario->assumptions,
                ];
            }),
            'negotiation_points' => $analysis->negotiation_points ? explode("\n", $analysis->negotiation_points) : [],
        ];

        return response()->json($presentationData);
    }

    /**
     * Get recommendation text in Arabic.
     */
    private function getRecommendationText($recommendation): string
    {
        return match($recommendation) {
            'continue' => 'يُوصى بالاستمرار مع المقاول الحالي',
            'negotiate' => 'يُوصى بإعادة التفاوض',
            'review' => 'يتطلب مراجعة دقيقة',
            default => 'غير محدد',
        };
    }
}
