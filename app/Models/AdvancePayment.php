<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvancePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'advance_number',
        'project_id',
        'contract_id',
        'advance_type',
        'advance_percentage',
        'advance_amount',
        'currency_id',
        'payment_date',
        'guarantee_required',
        'guarantee_id',
        'recovery_start_percentage',
        'recovery_percentage',
        'recovered_amount',
        'balance_amount',
        'status',
        'approved_by_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'advance_percentage' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'recovery_start_percentage' => 'decimal:2',
        'recovery_percentage' => 'decimal:2',
        'recovered_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'guarantee_required' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->advance_number)) {
                $model->advance_number = static::generateAdvanceNumber();
            }
        });
    }

    protected static function generateAdvanceNumber(): string
    {
        $year = date('Y');
        $lastAdvance = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastAdvance ? intval(substr($lastAdvance->advance_number, -4)) + 1 : 1;
        
        return sprintf('ADV-%s-%04d', $year, $nextNumber);
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

    public function guarantee(): BelongsTo
    {
        return $this->belongsTo(RetentionGuarantee::class, 'guarantee_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function recoveries(): HasMany
    {
        return $this->hasMany(AdvanceRecovery::class);
    }
}
