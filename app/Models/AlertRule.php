<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'description',
        'event_type',
        'conditions',
        'recipients_type',
        'recipients_ids',
        'channels',
        'message_template',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'conditions' => 'array',
        'recipients_ids' => 'array',
        'channels' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    // Methods
    public function toggle()
    {
        $this->update(['is_active' => ! $this->is_active]);

        return $this->is_active;
    }

    public function matchesConditions(array $data)
    {
        if (empty($this->conditions)) {
            return true;
        }

        foreach ($this->conditions as $key => $value) {
            if (! isset($data[$key]) || $data[$key] != $value) {
                return false;
            }
        }

        return true;
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
