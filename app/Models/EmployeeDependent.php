<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDependent extends Model
{
    protected $fillable = [
        'employee_id',
        'full_name',
        'relationship',
        'date_of_birth',
        'national_id',
        'is_covered_by_insurance',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_covered_by_insurance' => 'boolean',
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
