<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeBarAlert extends Model
{
    protected $fillable = [
        'event_id',
        'alert_type',
        'days_remaining',
        'sent_at',
        'sent_to',
        'channel',
        'acknowledged',
        'acknowledged_by',
        'acknowledged_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'acknowledged' => 'boolean',
        'sent_to' => 'array',
        'days_remaining' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(TimeBarEvent::class, 'event_id');
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    // Scopes
    public function scopeUnacknowledged($query)
    {
        return $query->where('acknowledged', false);
    }

    public function scopeCritical($query)
    {
        return $query->whereIn('alert_type', ['critical_warning', 'final_warning', 'expired']);
    }

    public function getAlertTypeLabel(): string
    {
        $labels = [
            'first_warning' => 'تنبيه أول',
            'second_warning' => 'تنبيه ثاني',
            'urgent_warning' => 'تنبيه عاجل',
            'critical_warning' => 'تنبيه حرج',
            'final_warning' => 'تنبيه أخير',
            'expired' => 'انتهى الموعد',
        ];

        return $labels[$this->alert_type] ?? $this->alert_type;
    }
}
