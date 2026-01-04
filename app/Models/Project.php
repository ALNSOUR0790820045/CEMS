<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'description',
        'company_id',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function regretAnalyses()
    {
        return $this->hasMany(FinancialRegretAnalysis::class);
    }
}
