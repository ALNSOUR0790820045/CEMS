<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontractorIpcItem extends Model
{
    protected $fillable = [
        'subcontractor_ipc_id',
        'description',
        'unit_id',
        'agreement_quantity',
        'unit_rate',
        'previous_quantity',
        'current_quantity',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'agreement_quantity' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'previous_quantity' => 'decimal:2',
        'current_quantity' => 'decimal:2',
    ];

    protected $appends = [
        'cumulative_quantity',
        'current_amount',
    ];

    // Relationships
    public function ipc(): BelongsTo
    {
        return $this->belongsTo(SubcontractorIpc::class, 'subcontractor_ipc_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Computed Attributes
    public function getCumulativeQuantityAttribute(): float
    {
        return (float) $this->current_quantity + (float) $this->previous_quantity;
    }

    public function getCurrentAmountAttribute(): float
    {
        return (float) $this->current_quantity * (float) $this->unit_rate;
    }
}
