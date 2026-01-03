<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'email',
        'phone',
        'address',
        'contact_person',
        'tax_number',
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

    public function materialVendors()
    {
        return $this->hasMany(MaterialVendor::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'material_vendors')
            ->withPivot('vendor_material_code', 'unit_price', 'currency_id', 'lead_time_days', 'min_order_quantity', 'is_preferred')
            ->withTimestamps();
    }
}
