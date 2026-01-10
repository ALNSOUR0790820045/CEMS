<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeBarClaim extends Model
{
    protected $fillable = [
        'project_id',
        'claim_number',
        'notice_date',
        'description',
        'status',
    ];

    protected $casts = [
        'notice_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
