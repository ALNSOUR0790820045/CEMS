<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceRequest extends Model
{
    protected $fillable = [
        'request_number',
        'project_id',
        'request_date',
        'required_by',
        'status',
        'notes',
        'requested_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_by' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(PriceRequestItem::class);
    }

    public function quotations()
    {
        return $this->hasMany(PriceQuotation::class);
    }

    public function comparison()
    {
        return $this->hasOne(PriceComparison::class);
    }
}
