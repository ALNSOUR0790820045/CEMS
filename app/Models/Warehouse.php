<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'warehouse_code',
        'warehouse_name',
        'warehouse_type',
        'address',
        'city',
        'country',
        'manager_id',
        'phone',
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

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function locations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }

    public function stock()
    {
        return $this->hasMany(WarehouseStock::class);
    }
}
