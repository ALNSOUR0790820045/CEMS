<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_id',
        'cost_category_id',
        'gl_account_id',
        'month',
        'budgeted_amount',
        'actual_amount',
        'variance',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    // Relationships
    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function costCategory(): BelongsTo
    {
        return $this->belongsTo(CostCategory::class);
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class);
    }

    // Helper methods
    public function calculateVariance()
    {
        $this->variance = $this->budgeted_amount - $this->actual_amount;
        return $this->variance;
    }

    public function getVariancePercentageAttribute()
    {
        if ($this->budgeted_amount == 0) {
            return 0;
        }
        return ($this->variance / $this->budgeted_amount) * 100;
    }
}
