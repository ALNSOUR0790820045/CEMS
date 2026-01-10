<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefectNotification extends Model
{
    protected $fillable = [
        'defects_liability_id',
        'notification_number',
        'notification_date',
        'notified_by',
        'defect_description',
        'location',
        'severity',
        'rectification_deadline',
        'rectification_date',
        'status',
        'cost_to_rectify',
        'deducted_from_retention',
        'photos',
        'remarks',
    ];

    protected $casts = [
        'notification_date' => 'date',
        'rectification_deadline' => 'date',
        'rectification_date' => 'date',
        'cost_to_rectify' => 'decimal:2',
        'deducted_from_retention' => 'decimal:2',
        'photos' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->notification_number)) {
                $model->notification_number = static::generateNotificationNumber();
            }
        });
    }

    protected static function generateNotificationNumber(): string
    {
        $year = date('Y');
        $lastNotification = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastNotification ? intval(substr($lastNotification->notification_number, -4)) + 1 : 1;
        
        return sprintf('DN-%s-%04d', $year, $nextNumber);
    }

    public function defectsLiability(): BelongsTo
    {
        return $this->belongsTo(DefectsLiability::class);
    }
}
