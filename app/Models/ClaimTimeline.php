<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimTimeline extends Model
{
    protected $table = 'claim_timeline';

    protected $fillable = [
        'claim_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'performed_by',
    ];

    // Relationships
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
