<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'budget_name',
        'fiscal_year',
        'budget_type',
        'project_id',
        'cost_center_id',
        'status',
        'total_budget',
        'company_id',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'total_budget' => 'decimal:2',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }

    // Helper methods
    public function getTotalActualAmount()
    {
        return $this->budgetItems()->sum('actual_amount');
    }

    public function getTotalVariance()
    {
        return $this->getTotalActualAmount() - $this->total_budget;
    }
}
