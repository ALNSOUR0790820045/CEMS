<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number',
        'name',
        'client_id',
        'tender_id',
        'contract_date',
        'contract_value',
        'status',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_value' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
