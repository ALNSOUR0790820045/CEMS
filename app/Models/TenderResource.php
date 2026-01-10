<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderResource extends Model
{
    protected $fillable = [
        'tender_id',
        'resource_code',
        'name',
        'name_en',
        'resource_type',
        'category',
        'skill_level',
        'unit_cost',
        'cost_unit',
        'required_quantity',
        'quantity_unit',
        'is_available',
        'available_from',
        'available_to',
        'total_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'required_quantity' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'is_available' => 'boolean',
        'available_from' => 'date',
        'available_to' => 'date',
    ];

    // Relationships
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function assignments()
    {
        return $this->hasMany(TenderResourceAssignment::class);
    }

    public function histograms()
    {
        return $this->hasMany(TenderResourceHistogram::class);
    }

    // Helper methods
    public function calculateTotalCost()
    {
        return $this->unit_cost * $this->required_quantity;
    }
}
