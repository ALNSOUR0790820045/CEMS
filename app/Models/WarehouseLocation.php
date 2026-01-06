<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'warehouse_id',
        'location_code',
        'location_name',
        'location_type',
        'parent_location_id',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'decimal:2',
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function parentLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'parent_location_id');
    }

    public function childLocations()
    {
        return $this->hasMany(WarehouseLocation::class, 'parent_location_id');
    }

    public function stock()
    {
        return $this->hasMany(WarehouseStock::class, 'location_id');
    }
}
