<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryInstruction extends Model
{
    protected $fillable = [
        'site_diary_id',
        'instruction_type',
        'issued_by',
        'received_by_id',
        'description',
        'action_required',
        'deadline',
        'status',
        'reference_number',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relationships
    public function siteDiary(): BelongsTo
    {
        return $this->belongsTo(SiteDiary::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('deadline', '<', now());
    }
}
