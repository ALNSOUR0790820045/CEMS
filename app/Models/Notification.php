<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'notification_type',
        'title',
        'message',
        'priority',
        'user_id',
        'role_id',
        'related_entity_type',
        'related_entity_id',
        'is_read',
        'read_at',
        'sent_via_email',
        'sent_via_sms',
        'email_sent_at',
        'sms_sent_at',
        'company_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_via_email' => 'boolean',
        'sent_via_sms' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'sms_sent_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
