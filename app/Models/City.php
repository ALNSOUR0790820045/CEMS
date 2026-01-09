<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'country_code',
    ];

    // Relationships
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}
