<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryIncident extends Model
{
    protected $fillable = [
        'site_diary_id',
        'incident_type',
        'severity',
        'time_occurred',
        'location',
        'description',
        'persons_involved',
        'injuries',
        'property_damage',
        'immediate_action',
        'reported_to',
        'hse_notified',
        'investigation_required',
        'photos',
    ];

    protected $casts = [
        'time_occurred' => 'datetime',
        'hse_notified' => 'boolean',
        'investigation_required' => 'boolean',
        'photos' => 'array',
    ];

    // Relationships
    public function siteDiary(): BelongsTo
    {
        return $this->belongsTo(SiteDiary::class);
    }
}
