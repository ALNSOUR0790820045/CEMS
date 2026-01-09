<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAsset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_code',
        'asset_name',
        'asset_name_en',
        'description',
        'category_id',
        'subcategory_id',
        'serial_number',
        'barcode',
        'acquisition_date',
        'acquisition_cost',
        'currency_id',
        'useful_life_years',
        'useful_life_months',
        'salvage_value',
        'depreciation_method',
        'depreciation_rate',
        'accumulated_depreciation',
        'net_book_value',
        'status',
        'location_id',
        'department_id',
        'project_id',
        'assigned_to_id',
        'vendor_id',
        'purchase_order_id',
        'warranty_expiry_date',
        'insurance_policy_number',
        'insurance_expiry_date',
        'gl_asset_account_id',
        'gl_depreciation_account_id',
        'gl_accumulated_depreciation_account_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'useful_life_years' => 'integer',
        'useful_life_months' => 'integer',
        'salvage_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'net_book_value' => 'decimal:2',
        'warranty_expiry_date' => 'date',
        'insurance_expiry_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->asset_code)) {
                $asset->asset_code = static::generateAssetCode();
            }
            
            // Set net book value initially
            if (is_null($asset->net_book_value)) {
                $asset->net_book_value = $asset->acquisition_cost - ($asset->accumulated_depreciation ?? 0);
            }
        });

        static::updating(function ($asset) {
            // Update net book value when accumulated depreciation changes
            if ($asset->isDirty('accumulated_depreciation') || $asset->isDirty('acquisition_cost')) {
                $asset->net_book_value = $asset->acquisition_cost - $asset->accumulated_depreciation;
            }
        });
    }

    public static function generateAssetCode(): string
    {
        $year = date('Y');
        $prefix = "FA-{$year}-";
        
        $lastAsset = static::where('asset_code', 'like', $prefix . '%')
            ->orderBy('asset_code', 'desc')
            ->first();
        
        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->asset_code, -4);
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'subcategory_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function glAssetAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'gl_asset_account_id');
    }

    public function glDepreciationAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'gl_depreciation_account_id');
    }

    public function glAccumulatedDepreciationAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'gl_accumulated_depreciation_account_id');
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class);
    }

    public function disposals(): HasMany
    {
        return $this->hasMany(AssetDisposal::class);
    }

    public function revaluations(): HasMany
    {
        return $this->hasMany(AssetRevaluation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('asset_code', 'like', "%{$search}%")
                ->orWhere('asset_name', 'like', "%{$search}%")
                ->orWhere('asset_name_en', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function calculateMonthlyDepreciation(): float
    {
        if ($this->depreciation_method === 'straight_line') {
            return $this->calculateStraightLineDepreciation();
        } elseif ($this->depreciation_method === 'declining_balance') {
            return $this->calculateDecliningBalanceDepreciation();
        }
        
        return 0;
    }

    protected function calculateStraightLineDepreciation(): float
    {
        $totalMonths = ($this->useful_life_years * 12) + ($this->useful_life_months ?? 0);
        
        if ($totalMonths <= 0) {
            return 0;
        }
        
        $depreciableAmount = $this->acquisition_cost - $this->salvage_value;
        return $depreciableAmount / $totalMonths;
    }

    protected function calculateDecliningBalanceDepreciation(): float
    {
        if (!$this->depreciation_rate || $this->depreciation_rate <= 0) {
            return 0;
        }
        
        $rate = $this->depreciation_rate / 100;
        $monthlyRate = $rate / 12;
        
        return $this->net_book_value * $monthlyRate;
    }

    public function isWarrantyExpired(): bool
    {
        return $this->warranty_expiry_date && $this->warranty_expiry_date < now();
    }

    public function isInsuranceExpired(): bool
    {
        return $this->insurance_expiry_date && $this->insurance_expiry_date < now();
    }
}
