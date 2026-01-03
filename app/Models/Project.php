<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_code',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'company_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function apInvoices()
    {
        return $this->hasMany(ApInvoice::class);
    }

    public function apInvoiceItems()
    {
        return $this->hasMany(ApInvoiceItem::class);
    }
}
