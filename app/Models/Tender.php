<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tender_number',
        'name',
        'description',
        'client_id',
        'estimated_value',
        'submission_date',
        'status',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'submission_date' => 'date',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
