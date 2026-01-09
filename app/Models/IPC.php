<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IPC extends Model
{
    use SoftDeletes;

    protected $table = 'i_p_c_s';

    protected $fillable = [
        'ipc_number',
        'contract_id',
        'project_id',
        'ipc_date',
        'amount',
        'description',
        'status',
        'company_id',
    ];

    protected $casts = [
        'ipc_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function arInvoices()
    {
        return $this->hasMany(ARInvoice::class);
    }
}
