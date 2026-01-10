<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PunchItemHistory extends Model
{
    protected $fillable = [
        'punch_item_id',
        'action',
        'old_value',
        'new_value',
        'performed_by_id',
        'performed_at',
        'remarks',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    // Relationships
    public function punchItem(): BelongsTo
    {
        return $this->belongsTo(PunchItem::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }
}
