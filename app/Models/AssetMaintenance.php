<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'maintenance_number',
        'fixed_asset_id',
        'maintenance_type',
        'maintenance_date',
        'description',
        'vendor_id',
        'cost',
        'is_capitalized',
        'next_maintenance_date',
        'status',
        'performed_by',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'decimal:2',
        'is_capitalized' => 'boolean',
        'next_maintenance_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($maintenance) {
            if (empty($maintenance->maintenance_number)) {
                $maintenance->maintenance_number = static::generateMaintenanceNumber();
            }
        });
    }

    public static function generateMaintenanceNumber(): string
    {
        $year = date('Y');
        $prefix = "MNT-{$year}-";
        
        $lastMaintenance = static::where('maintenance_number', 'like', $prefix . '%')
            ->orderBy('maintenance_number', 'desc')
            ->first();
        
        if ($lastMaintenance) {
            $lastNumber = (int) substr($lastMaintenance->maintenance_number, -4);
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

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    public function scopeByAsset($query, $assetId)
    {
        return $query->where('fixed_asset_id', $assetId);
    }

    public function scopeUpcoming($query, $days = 30)
    {
        $endDate = now()->addDays($days);
        return $query->where('next_maintenance_date', '<=', $endDate)
                     ->where('next_maintenance_date', '>=', now())
                     ->where('status', 'scheduled');
    }
}
