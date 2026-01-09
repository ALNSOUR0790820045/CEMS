<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommittedCost extends Model
{
    protected $fillable = [
        'project_id',
        'cost_code_id',
        'budget_item_id',
        'commitment_type',
        'commitment_id',
        'commitment_number',
        'vendor_id',
        'description',
        'original_amount',
        'approved_changes',
        'current_amount',
        'invoiced_amount',
        'remaining_amount',
        'currency_id',
        'status',
        'company_id',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'approved_changes' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'invoiced_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
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

    // Scopes
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'partially_invoiced']);
    }

    // Helper methods
    public function updateRemainingAmount(): void
    {
        $this->remaining_amount = $this->current_amount - $this->invoiced_amount;
        $this->save();
    }

    public function addInvoicedAmount(float $amount): void
    {
        $this->invoiced_amount += $amount;
        $this->updateRemainingAmount();
        
        // Update status based on invoiced amount
        if ($this->invoiced_amount >= $this->current_amount) {
            $this->status = 'closed';
        } elseif ($this->invoiced_amount > 0) {
            $this->status = 'partially_invoiced';
        }
        
        $this->save();
    }

    public function applyChange(float $changeAmount): void
    {
        $this->approved_changes += $changeAmount;
        $this->current_amount = $this->original_amount + $this->approved_changes;
        $this->updateRemainingAmount();
    }
}
