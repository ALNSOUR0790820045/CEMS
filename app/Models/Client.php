<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'contact_person',
        'tax_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
