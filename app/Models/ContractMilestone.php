<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractMilestone extends Model
{
    protected $fillable = [
        'contract_id',
        'milestone_number',
        'milestone_name',
        'description',
        'planned_date',
        'actual_date',
        'payment_percentage',
        'payment_amount',
        'status',
        'completion_percentage',
        'responsible_person_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'planned_date' => 'date',
        'actual_date' => 'date',
        'payment_percentage' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'completion_percentage' => 'decimal:2',
    ];

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function responsiblePerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDelayed($query)
    {
        return $query->where('status', 'delayed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('planned_date', '>=', now())
            ->where('status', 'not_started')
            ->orderBy('planned_date');
    }

    // Accessors
    public function getIsDelayedAttribute()
    {
        if ($this->status === 'completed' || ! $this->planned_date) {
            return false;
        }

        return \Carbon\Carbon::parse($this->planned_date)->isPast() && $this->status !== 'completed';
    }
}
