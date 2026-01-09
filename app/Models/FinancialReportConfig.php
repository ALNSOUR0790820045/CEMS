<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReportConfig extends Model
{
    protected $fillable = [
        'report_name',
        'report_type',
        'config_json',
        'is_active',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'config_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
