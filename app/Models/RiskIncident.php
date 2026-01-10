<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskIncident extends Model
{
    protected $fillable = [
        'incident_number',
        'risk_id',
        'project_id',
        'incident_date',
        'title',
        'description',
        'category',
        'actual_cost_impact',
        'actual_schedule_impact',
        'root_cause',
        'immediate_actions',
        'corrective_actions',
        'preventive_actions',
        'lessons_learned',
        'reported_by_id',
        'status',
        'company_id',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'actual_cost_impact' => 'decimal:2',
        'actual_schedule_impact' => 'integer',
    ];

    // Auto-generate incident number
    protected static function booted()
    {
        static::creating(function ($incident) {
            if (empty($incident->incident_number)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $incident->incident_number = sprintf('RI-%s-%04d', $year, $count);
            }
        });
    }

    // Relationships
    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_id');
    }

    // Helper methods
    public function resolve(): void
    {
        $this->update(['status' => 'resolved']);
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['reported', 'investigating']);
    }
}
