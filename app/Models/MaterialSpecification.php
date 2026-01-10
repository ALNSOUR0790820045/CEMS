<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialSpecification extends Model
{
    protected $fillable = [
        'material_id',
        'spec_name',
        'spec_value',
        'unit',
    ];

    // Relationships
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
