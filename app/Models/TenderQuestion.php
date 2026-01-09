<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderQuestion extends Model
{
    protected $fillable = [
        'tender_id',
        'question',
        'answer',
        'submitted_date',
        'answered_date',
    ];

    protected $casts = [
        'submitted_date' => 'date',
        'answered_date' => 'date',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
