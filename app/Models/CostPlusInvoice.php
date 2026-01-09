<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostPlusInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'cost_plus_contract_id',
        'project_id',
        'invoice_date',
        'period_from',
        'period_to',
        'material_costs',
        'labor_costs',
        'equipment_costs',
        'subcontract_costs',
        'overhead_costs',
        'other_costs',
        'total_direct_costs',
        'fee_amount',
        'incentive_amount',
        'subtotal',
        'vat_percentage',
        'vat_amount',
        'total_amount',
        'currency',
        'cumulative_costs',
        'gmp_remaining',
        'gmp_exceeded',
        'status',
        'prepared_by',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'material_costs' => 'decimal:2',
        'labor_costs' => 'decimal:2',
        'equipment_costs' => 'decimal:2',
        'subcontract_costs' => 'decimal:2',
        'overhead_costs' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'total_direct_costs' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'incentive_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cumulative_costs' => 'decimal:2',
        'gmp_remaining' => 'decimal:2',
        'gmp_exceeded' => 'boolean',
    ];

    public function costPlusContract(): BelongsTo
    {
        return $this->belongsTo(CostPlusContract::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CostPlusInvoiceItem::class, 'invoice_id');
    }

    public function calculateTotals(): void
    {
        $this->total_direct_costs = 
            $this->material_costs +
            $this->labor_costs +
            $this->equipment_costs +
            $this->subcontract_costs +
            $this->overhead_costs +
            $this->other_costs;

        $this->subtotal = $this->total_direct_costs + $this->fee_amount + $this->incentive_amount;
        $this->vat_amount = $this->subtotal * ($this->vat_percentage / 100);
        $this->total_amount = $this->subtotal + $this->vat_amount;
    }
}
