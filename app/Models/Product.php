<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD

class Product extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'sku',
        'description',
        'unit',
        'cost_price',
        'selling_price',
        'tax_rate',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
=======
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'code',
        'sku',
        'description',
        'type',
        'category',
        'unit',
        'unit_price',
        'barcode',
        'reorder_level',
        'min_stock',
        'max_stock',
        'track_inventory',
        'has_expiry',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'track_inventory' => 'boolean',
        'has_expiry' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function siteReceiptItems(): HasMany
    {
        return $this->hasMany(SiteReceiptItem::class);
    }
>>>>>>> main
}
