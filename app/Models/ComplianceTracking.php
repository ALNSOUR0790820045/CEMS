<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceTracking extends Model
{
    protected $table = 'compliance_tracking';

    protected $fillable = [
        'compliance_requirement_id',
        'entity_type',
        'entity_id',
        'due_date',
        'completion_date',
        'status',
        'responsible_person_id',
        'evidence_file_path',
        'remarks',
        'company_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completion_date' => 'date',
    ];

    // Auto-update status based on dates
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tracking) {
            if ($tracking->status === 'pending' && $tracking->due_date < now()) {
                $tracking->status = 'overdue';
            }
        });
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function complianceRequirement()
    {
        return $this->belongsTo(ComplianceRequirement::class);
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    public function entity()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'pending')
                    ->whereDate('due_date', '<', now());
            });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'completed' && $this->due_date < now();
    }

    public function getDaysUntilDueAttribute(): int
    {
        return now()->diffInDays($this->due_date, false);
    }
}
