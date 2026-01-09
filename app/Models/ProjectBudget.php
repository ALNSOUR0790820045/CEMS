<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectBudget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'budget_number',
        'project_id',
        'contract_id',
        'budget_type',
        'version',
        'budget_date',
        'contract_value',
        'direct_costs',
        'indirect_costs',
        'contingency_percentage',
        'contingency_amount',
        'total_budget',
        'profit_margin_percentage',
        'expected_profit',
        'currency_id',
        'status',
        'prepared_by_id',
        'approved_by_id',
        'approved_at',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'budget_date' => 'date',
        'contract_value' => 'decimal:2',
        'direct_costs' => 'decimal:2',
        'indirect_costs' => 'decimal:2',
        'contingency_percentage' => 'decimal:2',
        'contingency_amount' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'profit_margin_percentage' => 'decimal:2',
        'expected_profit' => 'decimal:2',
        'version' => 'integer',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectBudgetItem::class);
    }

    // Scopes
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('budget_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper methods
    public static function generateBudgetNumber($year = null)
    {
        $year = $year ?? date('Y');
        $lastBudget = static::whereYear('budget_date', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBudget && preg_match('/BUD-(\d{4})-(\d{4})/', $lastBudget->budget_number, $matches)) {
            $sequence = intval($matches[2]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('BUD-%s-%04d', $year, $sequence);
    }

    public function calculateTotalBudget(): void
    {
        $this->total_budget = $this->direct_costs + $this->indirect_costs + $this->contingency_amount;
        $this->save();
    }

    public function updateFromItems(): void
    {
        $totals = $this->items()->selectRaw('
            SUM(budgeted_amount) as total_budgeted,
            SUM(committed_amount) as total_committed,
            SUM(actual_amount) as total_actual
        ')->first();

        $this->total_budget = $totals->total_budgeted ?? 0;
    }
}
