<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function priceLists()
    {
        return $this->hasMany(PriceList::class);
    }
}
