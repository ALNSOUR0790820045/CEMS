<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderCompetitor extends Model
{
    protected $fillable = [
        'tender_id',
        'company_name',
        'offer_value',
        'rank',
        'is_winner',
        'notes',
    ];

    protected $casts = [
        'offer_value' => 'decimal:2',
        'is_winner' => 'boolean',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
