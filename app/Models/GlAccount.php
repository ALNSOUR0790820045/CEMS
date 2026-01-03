<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlAccount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'type',
        'parent_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parent()
    {
        return $this->belongsTo(GlAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(GlAccount::class, 'parent_id');
    }

    public function costAllocations()
    {
        return $this->hasMany(CostAllocation::class);
    }

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }
}
