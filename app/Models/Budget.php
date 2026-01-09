<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'budget_number',
        'budget_name',
        'fiscal_year',
        'budget_type',
        'cost_center_id',
        'project_id',
        'total_amount',
        'status',
        'approved_by_id',
        'approved_at',
        'company_id',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('budget_type', $type);
    }

    public function scopeByFiscalYear($query, $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Helper methods
    public static function generateBudgetNumber($fiscalYear = null)
    {
        $year = $fiscalYear ?? date('Y');
        $lastBudget = static::where('fiscal_year', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBudget && preg_match('/BDG-(\d{4})-(\d{4})/', $lastBudget->budget_number, $matches)) {
            $sequence = intval($matches[2]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('BDG-%s-%04d', $year, $sequence);
    }
}
