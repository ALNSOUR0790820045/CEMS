<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorrespondenceAction extends Model
{
    protected $fillable = [
        'correspondence_id',
        'user_id',
        'action',
        'comments',
        'forwarded_to',
    ];

    public function correspondence(): BelongsTo
    {
        return $this->belongsTo(Correspondence::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function forwardedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'forwarded_to');
    }
}
