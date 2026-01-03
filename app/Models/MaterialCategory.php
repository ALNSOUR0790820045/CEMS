<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'parent_id',
        'company_id',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parent()
    {
        return $this->belongsTo(MaterialCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MaterialCategory::class, 'parent_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'category_id');
    }
}
