<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GLFiscalYear extends Model
{
    protected $table = 'gl_fiscal_years';

    protected $fillable = [
        'year_name',
        'start_date',
        'end_date',
        'status',
        'is_current',
        'company_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Get the company that owns the fiscal year.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the periods for this fiscal year.
     */
    public function periods(): HasMany
    {
        return $this->hasMany(GLPeriod::class, 'fiscal_year_id');
    }

    /**
     * Scope to filter current fiscal year.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
