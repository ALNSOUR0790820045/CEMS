<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PunchItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_number',
        'punch_list_id',
        'location',
        'room_number',
        'grid_reference',
        'element',
        'description',
        'category',
        'severity',
        'discipline',
        'trade',
        'responsible_party',
        'assigned_to_id',
        'photos',
        'completion_photos',
        'due_date',
        'completed_date',
        'verified_date',
        'status',
        'rejection_reason',
        'completion_remarks',
        'verified_by_id',
        'cost_to_rectify',
        'back_charge',
        'priority',
    ];

    protected $casts = [
        'photos' => 'array',
        'completion_photos' => 'array',
        'due_date' => 'date',
        'completed_date' => 'date',
        'verified_date' => 'date',
        'cost_to_rectify' => 'decimal:2',
        'back_charge' => 'boolean',
    ];

    // Relationships
    public function punchList(): BelongsTo
    {
        return $this->belongsTo(PunchList::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PunchItemComment::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(PunchItemHistory::class);
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, ['completed', 'verified']);
    }

    public function addHistory(string $action, ?string $oldValue, ?string $newValue, int $performedBy, ?string $remarks = null): void
    {
        $this->history()->create([
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'performed_by_id' => $performedBy,
            'performed_at' => now(),
            'remarks' => $remarks,
        ]);
    }
}
