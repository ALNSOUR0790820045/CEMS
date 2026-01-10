<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressBillDeduction extends Model
{
    protected $fillable = [
        'progress_bill_id',
        'deduction_type',
        'description',
        'calculation_basis',
        'percentage',
        'base_amount',
        'amount',
        'reference',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function progressBill(): BelongsTo
    {
        return $this->belongsTo(ProgressBill::class);
    }

    // Business logic
    public function calculateAmount(): void
    {
        if ($this->calculation_basis === 'percentage' && $this->base_amount && $this->percentage) {
            $this->amount = $this->base_amount * ($this->percentage / 100);
            $this->save();
        }
    }
}
