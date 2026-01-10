<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retention extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'retention_number',
        'project_id',
        'contract_id',
        'retention_type',
        'retention_percentage',
        'max_retention_percentage',
        'release_schedule',
        'first_release_percentage',
        'first_release_condition',
        'second_release_percentage',
        'second_release_condition',
        'defects_liability_period_months',
        'dlp_start_date',
        'dlp_end_date',
        'total_contract_value',
        'total_retention_amount',
        'released_amount',
        'balance_amount',
        'currency_id',
        'status',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'dlp_start_date' => 'date',
        'dlp_end_date' => 'date',
        'retention_percentage' => 'decimal:2',
        'max_retention_percentage' => 'decimal:2',
        'first_release_percentage' => 'decimal:2',
        'second_release_percentage' => 'decimal:2',
        'total_contract_value' => 'decimal:2',
        'total_retention_amount' => 'decimal:2',
        'released_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->retention_number)) {
                $model->retention_number = static::generateRetentionNumber();
            }
        });
    }

    protected static function generateRetentionNumber(): string
    {
        $year = date('Y');
        $lastRetention = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastRetention ? intval(substr($lastRetention->retention_number, -4)) + 1 : 1;
        
        return sprintf('RET-%s-%04d', $year, $nextNumber);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function accumulations(): HasMany
    {
        return $this->hasMany(RetentionAccumulation::class);
    }

    public function releases(): HasMany
    {
        return $this->hasMany(RetentionRelease::class);
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(RetentionGuarantee::class);
    }

    public function defectsLiability(): HasMany
    {
        return $this->hasMany(DefectsLiability::class);
    }
}
