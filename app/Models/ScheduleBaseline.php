<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleBaseline extends Model
{
    protected $fillable = [
        'project_schedule_id',
        'baseline_number',
        'baseline_name',
        'baseline_date',
        'created_by_id',
        'notes',
    ];

    protected $casts = [
        'baseline_date' => 'date',
    ];

    // Relationships
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ProjectSchedule::class, 'project_schedule_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(BaselineActivity::class);
    }
}
