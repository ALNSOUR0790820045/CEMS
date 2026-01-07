<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'channel_email',
        'channel_sms',
        'channel_push',
        'channel_in_app',
        'is_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    protected $casts = [
        'channel_email' => 'boolean',
        'channel_sms' => 'boolean',
        'channel_push' => 'boolean',
        'channel_in_app' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function isChannelEnabled($channel)
    {
        return $this->is_enabled && $this->{"channel_{$channel}"};
    }

    public function isInQuietHours()
    {
        if (! $this->quiet_hours_start || ! $this->quiet_hours_end) {
            return false;
        }

        $now = now()->format('H:i:s');

        return $now >= $this->quiet_hours_start && $now <= $this->quiet_hours_end;
    }
}
