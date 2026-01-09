<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiValue extends Model
{
    protected $fillable = [
        'kpi_definition_id',
        'period_date',
        'actual_value',
        'target_value',
        'variance',
        'variance_percentage',
        'status',
        'project_id',
        'department_id',
        'company_id',
    ];

    protected $casts = [
        'period_date' => 'date',
        'actual_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'variance' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
    ];

    public function kpiDefinition(): BelongsTo
    {
        return $this->belongsTo(KpiDefinition::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_date', [$startDate, $endDate]);
    }
}
