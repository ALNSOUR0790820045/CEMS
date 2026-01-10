<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressBillVariation extends Model
{
    protected $fillable = [
        'progress_bill_id',
        'variation_order_id',
        'description',
        'quantity',
        'unit_id',
        'rate',
        'previous_amount',
        'current_amount',
        'cumulative_amount',
        'status',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'rate' => 'decimal:4',
        'previous_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'cumulative_amount' => 'decimal:2',
    ];

    // Relationships
    public function progressBill(): BelongsTo
    {
        return $this->belongsTo(ProgressBill::class);
    }

    public function variationOrder(): BelongsTo
    {
        return $this->belongsTo(VariationOrder::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
