<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderClarification extends Model
{
    protected $fillable = [
        'tender_id',
        'question_date',
        'question',
        'answer',
        'answer_date',
        'status',
        'asked_by',
    ];

    protected $casts = [
        'question_date' => 'date',
        'answer_date' => 'date',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function asker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asked_by');
    }
}
