<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborCategory extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function priceListItems()
    {
        return $this->hasMany(PriceListItem::class);
    }
}
