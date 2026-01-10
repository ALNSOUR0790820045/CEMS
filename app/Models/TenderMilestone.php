<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderMilestone extends Model
{
    protected $fillable = [
        'tender_id',
        'tender_activity_id',
        'name',
        'description',
        'target_date',
        'type',
        'is_critical',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'target_date' => 'date',
    ];

    // Relationships
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(TenderActivity::class, 'tender_activity_id');
    }
}
