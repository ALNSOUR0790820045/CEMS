<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'description',
        'reference_number',
        'issue_date',
        'submission_deadline',
        'budget',
        'status',
        'company_id',
        'is_active',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'submission_deadline' => 'date',
        'budget' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function boqItems()
    {
        return $this->hasMany(TenderBoqItem::class);
    }

    public function wbsItems()
    {
        return $this->hasMany(TenderWbs::class);
    }
}
