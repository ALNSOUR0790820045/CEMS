<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'name_en',
        'code',
        'description',
        'start_date',
        'planned_end_date',
        'actual_end_date',
        'total_budget',
        'contingency_budget',
        'status',
        'overall_progress',
        'location',
        'client_name',
        'project_manager_id',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_end_date' => 'date',
        'total_budget' => 'decimal:2',
        'contingency_budget' => 'decimal:2',
        'overall_progress' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class);
    }

    public function progressSnapshots(): HasMany
    {
        return $this->hasMany(ProjectProgressSnapshot::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(ProjectTimesheet::class);
    }

    public function baselines(): HasMany
    {
        return $this->hasMany(ProjectBaseline::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getTotalBudgetWithContingencyAttribute()
    {
        return $this->total_budget + $this->contingency_budget;
    }

    public function getCurrentBaseline()
    {
        return $this->baselines()->where('is_current', true)->first();
    }

    public function getLatestProgressSnapshot()
    {
        return $this->progressSnapshots()->latest('snapshot_date')->first();
    }
}
