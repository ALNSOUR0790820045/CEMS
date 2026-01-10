<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSchedule extends Model
{
    protected $fillable = [
        'report_type',
        'frequency',
        'schedule_time',
        'schedule_day',
        'email_recipients',
        'last_run_at',
        'next_run_at',
        'is_active',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'email_recipients' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
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
