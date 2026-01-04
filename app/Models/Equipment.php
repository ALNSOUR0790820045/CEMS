<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'equipment_number',
        'name',
        'name_en',
        'description',
        'category_id',
        'brand',
        'model',
        'year',
        'serial_number',
        'plate_number',
        'ownership',
        'rental_company',
        'rental_rate',
        'rental_rate_type',
        'purchase_price',
        'purchase_date',
        'current_value',
        'hourly_rate',
        'daily_rate',
        'operating_cost_per_hour',
        'capacity',
        'power',
        'fuel_type',
        'fuel_consumption',
        'status',
        'current_project_id',
        'current_location',
        'assigned_operator_id',
        'last_maintenance_date',
        'next_maintenance_date',
        'maintenance_interval_hours',
        'current_hours',
        'hours_since_last_maintenance',
        'insurance_company',
        'insurance_policy_number',
        'insurance_expiry_date',
        'registration_expiry_date',
        'image',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'insurance_expiry_date' => 'date',
        'registration_expiry_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'rental_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'operating_cost_per_hour' => 'decimal:2',
        'fuel_consumption' => 'decimal:2',
        'current_hours' => 'decimal:2',
        'hours_since_last_maintenance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    public function currentProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'current_project_id');
    }

    public function assignedOperator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_operator_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(EquipmentUsage::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(EquipmentFuelLog::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(EquipmentTransfer::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeNeedsMaintenance($query)
    {
        return $query->whereNotNull('next_maintenance_date')
            ->where('next_maintenance_date', '<=', now()->addDays(7));
    }
}
