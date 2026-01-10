<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskResponse extends Model
{
    protected $fillable = [
        'risk_id',
        'response_number',
        'response_type',
        'strategy',
        'description',
        'action_required',
        'responsible_id',
        'target_date',
        'actual_date',
        'cost_of_response',
        'effectiveness',
        'status',
        'remarks',
    ];

    protected $casts = [
        'target_date' => 'date',
        'actual_date' => 'date',
        'cost_of_response' => 'decimal:2',
    ];

    // Relationships
    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    // Helper methods
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'actual_date' => now(),
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->target_date && 
               $this->target_date->isPast() && 
               $this->status !== 'completed';
    }
}
