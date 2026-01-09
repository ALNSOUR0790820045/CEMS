<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActualCost extends Model
{
    protected $fillable = [
        'project_id',
        'cost_code_id',
        'budget_item_id',
        'transaction_date',
        'reference_type',
        'reference_id',
        'reference_number',
        'vendor_id',
        'description',
        'quantity',
        'unit_id',
        'unit_rate',
        'amount',
        'currency_id',
        'exchange_rate',
        'amount_local',
        'posted_by_id',
        'posted_at',
        'company_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'decimal:3',
        'unit_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'amount_local' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function costCode(): BelongsTo
    {
        return $this->belongsTo(CostCode::class);
    }

    public function budgetItem(): BelongsTo
    {
        return $this->belongsTo(ProjectBudgetItem::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_id');
    }

    // Scopes
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    public function scopeByReferenceType($query, $type)
    {
        return $query->where('reference_type', $type);
    }

    // Helper methods
    public function calculateLocalAmount(): void
    {
        $this->amount_local = $this->amount * $this->exchange_rate;
        $this->save();
    }
}
