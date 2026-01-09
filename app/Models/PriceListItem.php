<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id',
        'item_code',
        'item_name',
        'item_name_en',
        'description',
        'specifications',
        'unit',
        'unit_price',
        'min_price',
        'max_price',
        'material_id',
        'brand',
        'origin',
        'labor_category_id',
        'labor_rate_type',
        'equipment_category_id',
        'equipment_rate_type',
        'includes_operator',
        'includes_fuel',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'includes_operator' => 'boolean',
        'includes_fuel' => 'boolean',
        'unit_price' => 'decimal:4',
        'min_price' => 'decimal:4',
        'max_price' => 'decimal:4',
    ];

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function laborCategory()
    {
        return $this->belongsTo(LaborCategory::class);
    }

    public function equipmentCategory()
    {
        return $this->belongsTo(EquipmentCategory::class);
    }

    public function history()
    {
        return $this->hasMany(PriceHistory::class);
    }
}
