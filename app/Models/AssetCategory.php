<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'parent_id',
        'default_useful_life',
        'default_depreciation_method',
        'default_depreciation_rate',
        'gl_asset_account_id',
        'gl_depreciation_account_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'default_useful_life' => 'integer',
        'default_depreciation_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->code)) {
                $category->code = static::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        $prefix = "AC-";
        
        $lastCategory = static::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();
        
        if ($lastCategory) {
            $lastNumber = (int) substr($lastCategory->code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AssetCategory::class, 'parent_id');
    }

    public function glAssetAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'gl_asset_account_id');
    }

    public function glDepreciationAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'gl_depreciation_account_id');
    }

    public function fixedAssets(): HasMany
    {
        return $this->hasMany(FixedAsset::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%");
        });
    }
}
