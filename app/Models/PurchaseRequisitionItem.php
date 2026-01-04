<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionItem extends Model
{
    protected $fillable = [
        'purchase_requisition_id',
        'material_id',
        'description',
        'quantity',
        'unit_id',
        'estimated_unit_price',
        'estimated_total',
        'converted_quantity',
        'specifications',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_unit_price' => 'decimal:2',
        'estimated_total' => 'decimal:2',
        'converted_quantity' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if ($item->quantity && $item->estimated_unit_price) {
                $item->estimated_total = $item->quantity * $item->estimated_unit_price;
            }
        });

        static::saved(function ($item) {
            $item->purchaseRequisition->updateTotalAmount();
        });

        static::deleted(function ($item) {
            $item->purchaseRequisition->updateTotalAmount();
        });
    }

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
