<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeBarSetting extends Model
{
    protected $fillable = [
        'project_id',
        'contract_id',
        'default_notice_period',
        'first_warning_days',
        'second_warning_days',
        'urgent_warning_days',
        'critical_warning_days',
        'final_warning_days',
        'email_notifications',
        'sms_notifications',
        'escalation_enabled',
        'notification_recipients',
        'escalation_recipients',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'escalation_enabled' => 'boolean',
        'notification_recipients' => 'array',
        'escalation_recipients' => 'array',
        'default_notice_period' => 'integer',
        'first_warning_days' => 'integer',
        'second_warning_days' => 'integer',
        'urgent_warning_days' => 'integer',
        'critical_warning_days' => 'integer',
        'final_warning_days' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public static function getForProjectOrContract(?int $projectId, ?int $contractId): ?self
    {
        // Try to get settings for specific contract first
        if ($contractId) {
            $setting = static::where('contract_id', $contractId)->first();
            if ($setting) {
                return $setting;
            }
        }

        // Then try project-level settings
        if ($projectId) {
            $setting = static::where('project_id', $projectId)
                ->whereNull('contract_id')
                ->first();
            if ($setting) {
                return $setting;
            }
        }

        // Return default settings
        return static::getDefaultSettings();
    }

    public static function getDefaultSettings(): self
    {
        $setting = new static();
        $setting->default_notice_period = 28;
        $setting->first_warning_days = 21;
        $setting->second_warning_days = 14;
        $setting->urgent_warning_days = 7;
        $setting->critical_warning_days = 3;
        $setting->final_warning_days = 1;
        $setting->email_notifications = true;
        $setting->sms_notifications = true;
        $setting->escalation_enabled = true;
        
        return $setting;
    }
}
