<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'type',
        'source',
        'effective_date',
        'expiry_date',
        'currency',
        'region_id',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PriceListItem::class);
    }
}
