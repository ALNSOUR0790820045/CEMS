<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeQualification extends Model
{
    protected $fillable = [
        'employee_id',
        'qualification_type',
        'qualification_name',
        'institution',
        'specialization',
        'start_date',
        'end_date',
        'grade',
        'attachment_path',
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
