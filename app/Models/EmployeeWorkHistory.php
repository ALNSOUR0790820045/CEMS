<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkHistory extends Model
{
    protected $table = 'employee_work_history';

    protected $fillable = [
        'employee_id',
        'company_name',
        'job_title',
        'start_date',
        'end_date',
        'responsibilities',
        'leaving_reason',
        'company_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
