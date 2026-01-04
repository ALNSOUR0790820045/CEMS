<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    protected $fillable = [
        'company_id',
        'project_id',
        'contract_number',
        'contract_name',
        'contract_type',
        'contract_value',
        'currency',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function costPlusContract(): HasOne
    {
        return $this->hasOne(CostPlusContract::class);
    }
}
