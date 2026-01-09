<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BOQRevision extends Model
{
    protected $fillable = [
        'boq_header_id',
        'revision_number',
        'revision_reason',
        'old_total',
        'new_total',
        'difference',
        'changes',
        'revised_by',
    ];

    protected $casts = [
        'old_total' => 'decimal:2',
        'new_total' => 'decimal:2',
        'difference' => 'decimal:2',
        'changes' => 'array',
    ];

    public function boqHeader(): BelongsTo
    {
        return $this->belongsTo(BOQHeader::class);
    }

    public function reviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by');
    }
}
