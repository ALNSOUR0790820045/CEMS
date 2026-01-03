<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number',
        'contract_name',
        'client_id',
        'project_id',
        'start_date',
        'end_date',
        'contract_value',
        'status',
        'company_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_value' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
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
