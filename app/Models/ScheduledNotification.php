<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'scheduled_at',
        'repeat_type',
        'recipients_type',
        'recipients_ids',
        'status',
        'created_by_id',
        'company_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'recipients_ids' => 'array',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'pending')
                     ->where('scheduled_at', '<=', now());
    }

    // Methods
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsSent()
    {
        $this->update(['status' => 'sent']);
    }

    public function getRecipients()
    {
        switch ($this->recipients_type) {
            case 'user':
                return User::whereIn('id', $this->recipients_ids ?? [])->get();
            case 'role':
                return User::role($this->recipients_ids ?? [])->get();
            case 'department':
                return User::whereIn('department_id', $this->recipients_ids ?? [])->get();
            case 'all':
                return User::where('company_id', $this->company_id)->get();
            default:
                return collect([]);
        }
    }
}
