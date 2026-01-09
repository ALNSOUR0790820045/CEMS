<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    protected $fillable = [
        'project_id',
        'report_number',
        'report_date',
        'weather_condition',
        'temperature',
        'humidity',
        'site_conditions',
        'work_start_time',
        'work_end_time',
        'total_work_hours',
        'workers_count',
        'workers_breakdown',
        'attendance_notes',
        'equipment_hours',
        'equipment_notes',
        'work_executed',
        'activities_progress',
        'quality_notes',
        'materials_received',
        'materials_notes',
        'problems',
        'delays',
        'safety_incidents',
        'visitors',
        'meetings',
        'instructions_received',
        'general_notes',
        'status',
        'prepared_by',
        'prepared_at',
        'reviewed_by',
        'reviewed_at',
        'consultant_approved_by',
        'consultant_approved_at',
        'client_approved_by',
        'client_approved_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'workers_breakdown' => 'array',
        'equipment_hours' => 'array',
        'activities_progress' => 'array',
        'materials_received' => 'array',
        'visitors' => 'array',
        'prepared_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'consultant_approved_at' => 'datetime',
        'client_approved_at' => 'datetime',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'total_work_hours' => 'decimal:2',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DailyReportPhoto::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function consultantApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_approved_by');
    }

    public function clientApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_approved_by');
    }

    // Helper Methods
    public static function generateReportNumber($year = null): string
    {
        $year = $year ?? date('Y');
        $lastReport = self::where('report_number', 'like', "DR-{$year}-%")
            ->orderBy('report_number', 'desc')
            ->first();

        if ($lastReport) {
            $lastNumber = (int) substr($lastReport->report_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('DR-%s-%03d', $year, $newNumber);
    }

    public function isFullySigned(): bool
    {
        return $this->prepared_at !== null 
            && $this->reviewed_at !== null 
            && $this->consultant_approved_at !== null;
    }

    public function canBeSigned(): bool
    {
        return $this->status === 'submitted';
    }
}
