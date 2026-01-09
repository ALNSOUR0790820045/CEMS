<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'company_id',
        'tender_code',
        'name',
        'name_en',
        'description',
        'status',
        'total_value',
        'submission_date',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function activities()
    {
        return $this->hasMany(TenderActivity::class);
    }

    public function resources()
    {
        return $this->hasMany(TenderResource::class);
    }

    public function histograms()
    {
        return $this->hasMany(TenderResourceHistogram::class);
    }
}
