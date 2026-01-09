<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBudgetItem extends Model
{
    protected $fillable = [
        'project_budget_id',
        'cost_code_id',
        'boq_item_id',
        'wbs_id',
        'description',
        'cost_type',
        'quantity',
        'unit_id',
        'unit_rate',
        'budgeted_amount',
        'committed_amount',
        'actual_amount',
        'variance_amount',
        'variance_percentage',
        'forecast_amount',
        'estimate_to_complete',
        'estimate_at_completion',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_rate' => 'decimal:2',
        'budgeted_amount' => 'decimal:2',
        'committed_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance_amount' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
        'forecast_amount' => 'decimal:2',
        'estimate_to_complete' => 'decimal:2',
        'estimate_at_completion' => 'decimal:2',
    ];

    // Relationships
    public function projectBudget(): BelongsTo
    {
        return $this->belongsTo(ProjectBudget::class);
    }

    public function costCode(): BelongsTo
    {
        return $this->belongsTo(CostCode::class);
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function wbs(): BelongsTo
    {
        return $this->belongsTo(ProjectWbs::class, 'wbs_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Helper methods
    public function calculateVariance(): void
    {
        $this->variance_amount = $this->budgeted_amount - $this->actual_amount;
        
        if ($this->budgeted_amount > 0) {
            $this->variance_percentage = ($this->variance_amount / $this->budgeted_amount) * 100;
        } else {
            $this->variance_percentage = 0;
        }
        
        $this->save();
    }

    public function calculateEAC(): void
    {
        // EAC = Actual + Estimate to Complete
        $this->estimate_at_completion = $this->actual_amount + $this->estimate_to_complete;
        $this->save();
    }

    public function updateActualAmount(float $amount): void
    {
        $this->actual_amount += $amount;
        $this->calculateVariance();
        $this->save();
    }

    public function updateCommittedAmount(float $amount): void
    {
        $this->committed_amount += $amount;
        $this->save();
    }
}
