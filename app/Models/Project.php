<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'description',
        'start_date',
        'end_date',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function priceRequests()
    {
        return $this->hasMany(PriceRequest::class);
    }
}
