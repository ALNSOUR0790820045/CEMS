<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskMonitoring extends Model
{
    protected $table = 'risk_monitoring';

    protected $fillable = [
        'risk_id',
        'monitoring_date',
        'monitored_by_id',
        'current_status',
        'probability_change',
        'impact_change',
        'trigger_status',
        'early_warning_signs',
        'actions_taken',
        'effectiveness',
        'recommendations',
        'next_review_date',
    ];

    protected $casts = [
        'monitoring_date' => 'date',
        'next_review_date' => 'date',
    ];

    // Relationships
    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    public function monitoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'monitored_by_id');
    }

    // Helper methods
    public function hasWarning(): bool
    {
        return $this->trigger_status === 'warning' || $this->trigger_status === 'triggered';
    }

    public function riskIncreased(): bool
    {
        return $this->probability_change === 'increased' || $this->impact_change === 'increased';
    }
}
