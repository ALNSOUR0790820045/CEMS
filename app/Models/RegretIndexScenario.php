<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegretIndexScenario extends Model
{
    protected $fillable = [
        'analysis_id',
        'scenario_name',
        'scenario_type',
        'assumptions',
        'regret_index',
    ];

    protected $casts = [
        'assumptions' => 'array',
        'regret_index' => 'decimal:2',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(FinancialRegretAnalysis::class, 'analysis_id');
    }

    /**
     * Calculate regret index for this scenario based on assumptions
     */
    public function calculateScenarioRegretIndex(FinancialRegretAnalysis $baseAnalysis): void
    {
        $assumptions = $this->assumptions;

        // Apply multipliers from assumptions
        $continuationCost = $baseAnalysis->continuation_remaining_cost * ($assumptions['continuation_cost_multiplier'] ?? 1);
        $continuationClaims = $baseAnalysis->continuation_claims_estimate * ($assumptions['continuation_claims_multiplier'] ?? 1);
        $continuationVariations = $baseAnalysis->continuation_variations * ($assumptions['continuation_variations_multiplier'] ?? 1);

        $terminationPayment = $baseAnalysis->termination_payment_due * ($assumptions['termination_payment_multiplier'] ?? 1);
        $terminationClaims = $baseAnalysis->termination_claims * ($assumptions['termination_claims_multiplier'] ?? 1);
        $terminationLegal = $baseAnalysis->termination_legal_costs * ($assumptions['termination_legal_multiplier'] ?? 1);

        $newContractorWork = $baseAnalysis->new_contractor_remaining_work * ($assumptions['new_contractor_work_multiplier'] ?? 1);
        $delayDays = $baseAnalysis->estimated_delay_days * ($assumptions['delay_days_multiplier'] ?? 1);

        // Calculate totals for this scenario
        $costToContinue = $continuationCost + $continuationClaims + $continuationVariations;
        
        $costToTerminate = $terminationPayment 
            + $baseAnalysis->termination_demobilization 
            + $terminationClaims 
            + $terminationLegal
            + $baseAnalysis->new_contractor_mobilization
            + $baseAnalysis->new_contractor_learning_curve
            + $baseAnalysis->new_contractor_premium
            + $newContractorWork
            + ($delayDays * $baseAnalysis->delay_cost_per_day);

        // Calculate regret index for scenario
        $this->regret_index = $costToTerminate - $costToContinue;
    }
}
