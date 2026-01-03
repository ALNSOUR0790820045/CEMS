<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'material_code',
        'name',
        'name_en',
        'description',
        'material_type',
        'category_id',
        'unit_id',
        'reorder_level',
        'min_stock',
        'max_stock',
        'standard_cost',
        'selling_price',
        'currency_id',
        'barcode',
        'sku',
        'specifications',
        'image_path',
        'is_active',
        'is_stockable',
        'company_id',
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
        'is_stockable' => 'boolean',
        'reorder_level' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'standard_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    // Auto-generate material code
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($material) {
            if (empty($material->material_code)) {
                $year = date('Y');
                // Use max() to get the highest ID more efficiently than ordering
                $lastMaterial = static::where('material_code', 'like', 'MAT-' . $year . '-%')
                    ->orderBy('material_code', 'desc')
                    ->first();
                
                $nextNumber = $lastMaterial ? intval(substr($lastMaterial->material_code, -4)) + 1 : 1;
                $material->material_code = 'MAT-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'material_vendors')
            ->withPivot('vendor_material_code', 'unit_price', 'currency_id', 'lead_time_days', 'min_order_quantity', 'is_preferred')
            ->withTimestamps();
    }

    public function materialVendors()
    {
        return $this->hasMany(MaterialVendor::class);
    }

    public function specifications()
    {
        return $this->hasMany(MaterialSpecification::class);
    }
}
