<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number',
        'project_id',
        'contractor_name',
        'contract_value',
        'start_date',
        'end_date',
        'duration_days',
        'status',
        'currency',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_days' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function regretAnalyses()
    {
        return $this->hasMany(FinancialRegretAnalysis::class);
    }
}
