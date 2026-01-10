<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderContingencyReserve extends Model
{
    protected $fillable = [
        'tender_id',
        'total_risk_exposure',
        'contingency_percentage',
        'contingency_amount',
        'justification',
    ];

    protected $casts = [
        'total_risk_exposure' => 'decimal:2',
        'contingency_percentage' => 'decimal:2',
        'contingency_amount' => 'decimal:2',
    ];

    // Relationships
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    // Auto-calculate contingency amount
    protected static function booted()
    {
        static::saving(function ($reserve) {
            $reserve->contingency_amount = ($reserve->total_risk_exposure * $reserve->contingency_percentage) / 100;
        });
    }
}
