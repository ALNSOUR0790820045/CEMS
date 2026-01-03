<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_number',
        'project_name',
        'client_id',
        'start_date',
        'end_date',
        'status',
        'company_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function ipcs()
    {
        return $this->hasMany(IPC::class);
    }

    public function arInvoices()
    {
        return $this->hasMany(ARInvoice::class);
    }
}
