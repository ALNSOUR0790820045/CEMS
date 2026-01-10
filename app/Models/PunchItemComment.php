<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PunchItemComment extends Model
{
    protected $fillable = [
        'punch_item_id',
        'comment',
        'commented_by_id',
        'comment_type',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    // Relationships
    public function punchItem(): BelongsTo
    {
        return $this->belongsTo(PunchItem::class);
    }

    public function commentedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commented_by_id');
    }
}
