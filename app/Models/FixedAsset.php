<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'asset_name',
        'asset_category',
        'asset_type',
        'purchase_date',
        'purchase_cost',
        'supplier_id',
        'serial_number',
        'location',
        'department_id',
        'custodian_id',
        'depreciation_method',
        'useful_life_years',
        'salvage_value',
        'accumulated_depreciation',
        'status',
        'gl_asset_account_id',
        'gl_depreciation_account_id',
        'gl_accumulated_depreciation_account_id',
        'warranty_expiry_date',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'useful_life_years' => 'integer',
    ];

    protected $appends = ['book_value'];

    // Computed attribute for book_value
    public function getBookValueAttribute()
    {
        return $this->purchase_cost - $this->accumulated_depreciation;
    }

    // Generate unique asset code
    public static function generateAssetCode()
    {
        $year = date('Y');
        $prefix = 'FA-' . $year . '-';
        
        $lastAsset = self::where('asset_code', 'like', $prefix . '%')
            ->orderBy('asset_code', 'desc')
            ->first();
        
        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->asset_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    public function disposal()
    {
        return $this->hasOne(AssetDisposal::class);
    }

    // Calculate depreciation for a period
    public function calculateDepreciation($periodDate)
    {
        $monthsElapsed = now()->parse($this->purchase_date)->diffInMonths($periodDate);
        
        if ($monthsElapsed <= 0) {
            return 0;
        }

        switch ($this->depreciation_method) {
            case 'straight_line':
                $annualDepreciation = ($this->purchase_cost - $this->salvage_value) / $this->useful_life_years;
                return round($annualDepreciation / 12, 2); // Monthly depreciation
                
            case 'declining_balance':
                $rate = 2 / $this->useful_life_years; // Double declining balance
                $currentBookValue = $this->book_value;
                $monthlyDepreciation = ($currentBookValue * $rate) / 12;
                
                // Ensure we don't depreciate below salvage value
                $maxDepreciation = max(0, $currentBookValue - $this->salvage_value);
                return round(min($monthlyDepreciation, $maxDepreciation), 2);
                
            case 'units_of_production':
                // This would require additional fields for units produced
                // For now, default to straight line
                $annualDepreciation = ($this->purchase_cost - $this->salvage_value) / $this->useful_life_years;
                return round($annualDepreciation / 12, 2);
                
            default:
                return 0;
        }
    }
}
