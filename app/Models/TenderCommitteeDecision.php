<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderCommitteeDecision extends Model
{
    protected $fillable = [
        'tender_id',
        'meeting_date',
        'attendees',
        'decision',
        'reasons',
        'conditions',
        'approved_budget',
        'chairman_id',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'attendees' => 'array',
        'approved_budget' => 'decimal:2',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function chairman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chairman_id');
    }
}
