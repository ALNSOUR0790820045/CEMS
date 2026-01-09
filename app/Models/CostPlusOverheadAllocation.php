<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostPlusOverheadAllocation extends Model
{
    protected $fillable = [
        'cost_plus_contract_id',
        'project_id',
        'year',
        'month',
        'overhead_type',
        'description',
        'total_overhead',
        'allocation_percentage',
        'allocated_amount',
        'allocation_basis',
        'is_reimbursable',
        'allocated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'total_overhead' => 'decimal:2',
        'allocation_percentage' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'is_reimbursable' => 'boolean',
    ];

    public function costPlusContract(): BelongsTo
    {
        return $this->belongsTo(CostPlusContract::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function allocator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }
}
