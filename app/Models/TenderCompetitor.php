<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderCompetitor extends Model
{
    protected $fillable = [
        'tender_id',
        'company_name',
        'classification',
        'estimated_price',
        'strengths',
        'weaknesses',
        'notes',
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
