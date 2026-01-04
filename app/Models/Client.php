<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'commercial_registration',
        'tax_number',
        'type',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
