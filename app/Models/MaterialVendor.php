<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialVendor extends Model
{
    protected $fillable = [
        'material_id',
        'vendor_id',
        'vendor_material_code',
        'unit_price',
        'currency_id',
        'lead_time_days',
        'min_order_quantity',
        'is_preferred',
        'company_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'min_order_quantity' => 'decimal:2',
        'is_preferred' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
