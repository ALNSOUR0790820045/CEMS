<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionItem extends Model
{
    protected $fillable = [
        'purchase_requisition_id',
        'material_id',
        'item_description',
        'specifications',
        'quantity_requested',
        'unit_id',
        'estimated_unit_price',
        'estimated_total',
        'quantity_ordered',
        'preferred_vendor_id',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:3',
        'estimated_unit_price' => 'decimal:2',
        'estimated_total' => 'decimal:2',
        'quantity_ordered' => 'decimal:3',
    ];

    // Relationships
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

    public function preferredVendor()
    {
        return $this->belongsTo(Vendor::class, 'preferred_vendor_id');
    }

    public function quoteItems()
    {
        return $this->hasMany(PrQuoteItem::class, 'pr_item_id');
    }
}
