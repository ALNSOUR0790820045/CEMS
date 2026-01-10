<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GLPeriod extends Model
{
    protected $table = 'gl_periods';

    protected $fillable = [
        'fiscal_year_id',
        'period_number',
        'period_name',
        'start_date',
        'end_date',
        'status',
        'company_id',
    ];

    protected $casts = [
        'period_number' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the company that owns the period.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the fiscal year for this period.
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(GLFiscalYear::class, 'fiscal_year_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter open periods.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
