<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    // Type constants
    const TYPE_WEIGHT = 'weight';
    const TYPE_LENGTH = 'length';
    const TYPE_VOLUME = 'volume';
    const TYPE_QUANTITY = 'quantity';

    protected $fillable = [
        'name',
        'name_en',
        'code',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active units.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
