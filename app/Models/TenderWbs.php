<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderWbs extends Model
{
    protected $table = 'tender_wbs';

    protected $fillable = [
        'tender_id',
        'wbs_code',
        'name',
        'name_en',
        'description',
        'level',
        'parent_id',
        'sort_order',
        'estimated_cost',
        'materials_cost',
        'labor_cost',
        'equipment_cost',
        'subcontractor_cost',
        'estimated_duration_days',
        'weight_percentage',
        'is_active',
        'is_summary',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'materials_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'equipment_cost' => 'decimal:2',
        'subcontractor_cost' => 'decimal:2',
        'weight_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'is_summary' => 'boolean',
    ];

    // Relationships
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function parent()
    {
        return $this->belongsTo(TenderWbs::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TenderWbs::class, 'parent_id')->orderBy('sort_order');
    }

    public function boqItems()
    {
        return $this->belongsToMany(TenderBoqItem::class, 'tender_wbs_boq_mapping');
    }

    // Scopes
    public function scopeRootLevel($query)
    {
        return $query->where('level', 1)->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getTotalCostAttribute()
    {
        return $this->materials_cost + $this->labor_cost + $this->equipment_cost + $this->subcontractor_cost;
    }

    // Methods
    public function getDescendants()
    {
        $descendants = collect();
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }
        return $descendants;
    }

    public function calculateCostRollup()
    {
        if ($this->is_summary && $this->children()->count() > 0) {
            $totalCost = $this->children->sum(function ($child) {
                return $child->calculateCostRollup();
            });
            $this->estimated_cost = $totalCost;
            $this->save();
            return $totalCost;
        }
        return $this->estimated_cost;
    }
}
