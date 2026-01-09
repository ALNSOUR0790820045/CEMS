<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimCorrespondence extends Model
{
    protected $table = 'claim_correspondence';

    protected $fillable = [
        'claim_id',
        'reference_number',
        'date',
        'direction',
        'from',
        'to',
        'subject',
        'summary',
        'file_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationships
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
