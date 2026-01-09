<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'tax_number',
    ];

    public function salesQuotations()
    {
        return $this->hasMany(SalesQuotation::class);
    }
}
