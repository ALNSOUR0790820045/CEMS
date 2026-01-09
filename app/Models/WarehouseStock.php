<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $table = 'warehouse_stock';

    protected $fillable = [
        'warehouse_id',
        'location_id',
        'material_id',
        'quantity',
        'reserved_quantity',
        'batch_number',
        'expiry_date',
        'last_updated',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'expiry_date' => 'date',
        'last_updated' => 'datetime',
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Accessors
    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->reserved_quantity;
    }
}
