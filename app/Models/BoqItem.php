<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoqItem extends Model
{
    protected $fillable = [
        'project_id',
        'item_number',
        'description',
        'unit',
        'quantity',
        'unit_rate',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_rate' => 'decimal:4',
        'amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function variationOrderItems()
    {
        return $this->hasMany(VariationOrderItem::class);
    }
}
