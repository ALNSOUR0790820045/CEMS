<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'company_id',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function variationOrders()
    {
        return $this->hasMany(VariationOrder::class);
    }

    public function boqItems()
    {
        return $this->hasMany(BoqItem::class);
    }
}
