<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderTimeline extends Model
{
    protected $table = 'tender_timeline';

    protected $fillable = [
        'tender_id',
        'action',
        'from_status',
        'to_status',
        'description',
        'performed_by',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
