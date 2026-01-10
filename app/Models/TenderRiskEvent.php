<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderRiskEvent extends Model
{
    protected $fillable = [
        'tender_risk_id',
        'occurred_date',
        'description',
        'actual_cost_impact',
        'actual_schedule_impact_days',
        'actions_taken',
        'reported_by',
    ];

    protected $casts = [
        'occurred_date' => 'date',
        'actual_cost_impact' => 'decimal:2',
        'actual_schedule_impact_days' => 'integer',
    ];

    // Relationships
    public function tenderRisk(): BelongsTo
    {
        return $this->belongsTo(TenderRisk::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
