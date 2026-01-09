<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialRegretAnalysis extends Model
{
    protected $fillable = [
        'analysis_number',
        'project_id',
        'contract_id',
        'analysis_date',
        'contract_value',
        'work_completed_value',
        'work_completed_percentage',
        'remaining_work_value',
        'original_duration_days',
        'elapsed_days',
        'remaining_days',
        'continuation_remaining_cost',
        'continuation_claims_estimate',
        'continuation_variations',
        'continuation_total',
        'termination_payment_due',
        'termination_demobilization',
        'termination_claims',
        'termination_legal_costs',
        'termination_total',
        'new_contractor_mobilization',
        'new_contractor_learning_curve',
        'new_contractor_premium',
        'new_contractor_remaining_work',
        'new_contractor_total',
        'estimated_delay_days',
        'delay_cost_per_day',
        'total_delay_cost',
        'cost_to_terminate',
        'cost_to_continue',
        'regret_index',
        'regret_percentage',
        'currency',
        'recommendation',
        'analysis_notes',
        'negotiation_points',
        'prepared_by',
        'reviewed_by',
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'contract_value' => 'decimal:2',
        'work_completed_value' => 'decimal:2',
        'work_completed_percentage' => 'decimal:2',
        'remaining_work_value' => 'decimal:2',
        'original_duration_days' => 'integer',
        'elapsed_days' => 'integer',
        'remaining_days' => 'integer',
        'continuation_remaining_cost' => 'decimal:2',
        'continuation_claims_estimate' => 'decimal:2',
        'continuation_variations' => 'decimal:2',
        'continuation_total' => 'decimal:2',
        'termination_payment_due' => 'decimal:2',
        'termination_demobilization' => 'decimal:2',
        'termination_claims' => 'decimal:2',
        'termination_legal_costs' => 'decimal:2',
        'termination_total' => 'decimal:2',
        'new_contractor_mobilization' => 'decimal:2',
        'new_contractor_learning_curve' => 'decimal:2',
        'new_contractor_premium' => 'decimal:2',
        'new_contractor_remaining_work' => 'decimal:2',
        'new_contractor_total' => 'decimal:2',
        'estimated_delay_days' => 'integer',
        'delay_cost_per_day' => 'decimal:2',
        'total_delay_cost' => 'decimal:2',
        'cost_to_terminate' => 'decimal:2',
        'cost_to_continue' => 'decimal:2',
        'regret_index' => 'decimal:2',
        'regret_percentage' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scenarios(): HasMany
    {
        return $this->hasMany(RegretIndexScenario::class, 'analysis_id');
    }

    /**
     * Calculate all totals and regret index
     */
    public function calculateRegretIndex(): void
    {
        // Calculate continuation total
        $this->continuation_total = $this->continuation_remaining_cost 
            + $this->continuation_claims_estimate 
            + $this->continuation_variations;

        // Calculate termination total
        $this->termination_total = $this->termination_payment_due 
            + $this->termination_demobilization 
            + $this->termination_claims 
            + $this->termination_legal_costs;

        // Calculate new contractor total
        $this->new_contractor_total = $this->new_contractor_mobilization 
            + $this->new_contractor_learning_curve 
            + $this->new_contractor_premium 
            + $this->new_contractor_remaining_work;

        // Calculate delay cost
        $this->total_delay_cost = $this->estimated_delay_days * $this->delay_cost_per_day;

        // Calculate cost to continue
        $this->cost_to_continue = $this->continuation_total;

        // Calculate cost to terminate
        $this->cost_to_terminate = $this->termination_total 
            + $this->new_contractor_total 
            + $this->total_delay_cost;

        // Calculate regret index
        // مؤشر الندم = تكلفة الإنهاء - تكلفة الاستمرار
        // إذا كان موجباً = الأفضل الاستمرار
        $this->regret_index = $this->cost_to_terminate - $this->cost_to_continue;

        // Calculate regret percentage
        if ($this->contract_value > 0) {
            $this->regret_percentage = ($this->regret_index / $this->contract_value) * 100;
        } else {
            $this->regret_percentage = 0;
        }

        // Set recommendation based on regret index
        $this->recommendation = $this->determineRecommendation();
    }

    /**
     * Determine recommendation based on regret index
     */
    protected function determineRecommendation(): string
    {
        $percentage = abs($this->regret_percentage);

        if ($this->regret_index > 0) {
            // Cost to terminate is higher - continue is better
            if ($percentage > 20) {
                return 'continue';
            } elseif ($percentage > 10) {
                return 'negotiate';
            } else {
                return 'review';
            }
        } else {
            // Cost to continue is higher - termination might be better
            if ($percentage > 20) {
                return 'review';
            } else {
                return 'negotiate';
            }
        }
    }

    /**
     * Generate unique analysis number
     */
    public static function generateAnalysisNumber(): string
    {
        $date = now()->format('Ymd');
        $lastAnalysis = static::whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $sequence = $lastAnalysis ? (intval(substr($lastAnalysis->analysis_number, -4)) + 1) : 1;

        return 'FRA-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
