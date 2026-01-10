<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvanceRecovery extends Model
{
    protected $fillable = [
        'advance_payment_id',
        'progress_bill_id',
        'bill_date',
        'bill_amount',
        'recovery_percentage',
        'recovery_amount',
        'cumulative_recovery',
        'remaining_balance',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'bill_amount' => 'decimal:2',
        'recovery_percentage' => 'decimal:2',
        'recovery_amount' => 'decimal:2',
        'cumulative_recovery' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function advancePayment(): BelongsTo
    {
        return $this->belongsTo(AdvancePayment::class);
    }
}
