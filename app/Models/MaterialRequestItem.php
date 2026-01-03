<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequestItem extends Model
{
    protected $fillable = [
        'material_request_id',
        'material_id',
        'requested_quantity',
        'issued_quantity',
        'purpose',
        'specifications',
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:2',
        'issued_quantity' => 'decimal:2',
    ];

    protected $appends = ['remaining_quantity'];

    public function materialRequest()
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->requested_quantity - $this->issued_quantity;
    }
}
