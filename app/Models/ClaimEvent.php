<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimEvent extends Model
{
    protected $fillable = [
        'claim_id',
        'event_date',
        'description',
        'impact_type',
        'cost_impact',
        'time_impact_days',
    ];

    protected $casts = [
        'event_date' => 'date',
        'cost_impact' => 'decimal:2',
        'time_impact_days' => 'integer',
    ];

    // Relationships
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
