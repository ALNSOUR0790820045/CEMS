<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostPlusContract extends Model
{
    protected $fillable = [
        'contract_id',
        'project_id',
        'fee_type',
        'fee_percentage',
        'fixed_fee_amount',
        'has_gmp',
        'guaranteed_maximum_price',
        'gmp_savings_share',
        'overhead_reimbursable',
        'overhead_percentage',
        'overhead_method',
        'reimbursable_costs',
        'non_reimbursable_costs',
        'currency',
        'notes',
    ];

    protected $casts = [
        'fee_percentage' => 'decimal:2',
        'fixed_fee_amount' => 'decimal:2',
        'has_gmp' => 'boolean',
        'guaranteed_maximum_price' => 'decimal:2',
        'gmp_savings_share' => 'decimal:2',
        'overhead_reimbursable' => 'boolean',
        'overhead_percentage' => 'decimal:2',
        'reimbursable_costs' => 'array',
        'non_reimbursable_costs' => 'array',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CostPlusTransaction::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CostPlusInvoice::class);
    }

    public function overheadAllocations(): HasMany
    {
        return $this->hasMany(CostPlusOverheadAllocation::class);
    }

    public function calculateFee(float $costs): float
    {
        return match($this->fee_type) {
            'percentage' => $costs * ($this->fee_percentage / 100),
            'fixed_fee' => $this->fixed_fee_amount,
            'hybrid' => ($costs * ($this->fee_percentage / 100)) + $this->fixed_fee_amount,
            default => 0,
        };
    }

    public function checkGMPStatus(): array
    {
        if (!$this->has_gmp) {
            return ['exceeded' => false, 'remaining' => null];
        }

        $totalCosts = $this->invoices()->sum('cumulative_costs');
        $remaining = $this->guaranteed_maximum_price - $totalCosts;

        return [
            'exceeded' => $remaining < 0,
            'remaining' => $remaining,
            'percentage_used' => ($totalCosts / $this->guaranteed_maximum_price) * 100,
        ];
    }
}
