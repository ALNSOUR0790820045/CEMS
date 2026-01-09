<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderActivity extends Model
{
    protected $fillable = [
        'tender_id',
        'activity_code',
        'name',
        'name_en',
        'description',
        'duration',
        'start_date',
        'end_date',
        'budget',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    // Relationships
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function resourceAssignments()
    {
        return $this->hasMany(TenderResourceAssignment::class);
    }
}
