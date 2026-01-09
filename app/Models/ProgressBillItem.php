<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressBillItem extends Model
{
    protected $fillable = [
        'progress_bill_id',
        'boq_item_id',
        'item_code',
        'description',
        'unit_id',
        'contract_quantity',
        'contract_rate',
        'contract_amount',
        'previous_quantity',
        'previous_amount',
        'current_quantity',
        'current_amount',
        'cumulative_quantity',
        'cumulative_amount',
        'percentage_complete',
        'remaining_quantity',
        'remaining_amount',
        'remarks',
    ];

    protected $casts = [
        'contract_quantity' => 'decimal:4',
        'contract_rate' => 'decimal:4',
        'contract_amount' => 'decimal:2',
        'previous_quantity' => 'decimal:4',
        'previous_amount' => 'decimal:2',
        'current_quantity' => 'decimal:4',
        'current_amount' => 'decimal:2',
        'cumulative_quantity' => 'decimal:4',
        'cumulative_amount' => 'decimal:2',
        'percentage_complete' => 'decimal:2',
        'remaining_quantity' => 'decimal:4',
        'remaining_amount' => 'decimal:2',
    ];

    // Relationships
    public function progressBill(): BelongsTo
    {
        return $this->belongsTo(ProgressBill::class);
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Business logic
    public function calculateAmounts(): void
    {
        // Calculate cumulative
        $this->cumulative_quantity = $this->previous_quantity + $this->current_quantity;
        $this->cumulative_amount = $this->cumulative_quantity * $this->contract_rate;
        
        // Calculate current amount
        $this->current_amount = $this->current_quantity * $this->contract_rate;
        
        // Calculate percentage complete
        if ($this->contract_quantity > 0) {
            $this->percentage_complete = ($this->cumulative_quantity / $this->contract_quantity) * 100;
        }
        
        // Calculate remaining
        $this->remaining_quantity = $this->contract_quantity - $this->cumulative_quantity;
        $this->remaining_amount = $this->contract_amount - $this->cumulative_amount;
        
        $this->save();
    }
}
