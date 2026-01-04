<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'tender_number',
        'title',
        'description',
        'company_id',
        'submission_date',
        'opening_date',
        'status',
        'budget',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'opening_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function activities()
    {
        return $this->hasMany(TenderActivity::class);
    }

    public function wbsItems()
    {
        return $this->hasMany(TenderWbs::class);
    }
}
