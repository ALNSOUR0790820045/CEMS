<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderSiteVisit extends Model
{
    protected $fillable = [
        'tender_id',
        'visit_date',
        'visit_time',
        'attendees',
        'observations',
        'photos',
        'coordinates',
        'reported_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'attendees' => 'array',
        'photos' => 'array',
        'coordinates' => 'array',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
