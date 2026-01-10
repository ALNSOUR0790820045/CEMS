<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EotAffectedActivity extends Model
{
    protected $fillable = [
        'eot_claim_id',
        'activity_id',
        'original_end_date',
        'revised_end_date',
        'delay_days',
        'on_critical_path',
    ];

    protected $casts = [
        'original_end_date' => 'date',
        'revised_end_date' => 'date',
        'delay_days' => 'integer',
        'on_critical_path' => 'boolean',
    ];

    public function eotClaim(): BelongsTo
    {
        return $this->belongsTo(EotClaim::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }
}
