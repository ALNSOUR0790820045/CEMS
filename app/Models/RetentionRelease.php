<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionRelease extends Model
{
    protected $fillable = [
        'release_number',
        'retention_id',
        'release_type',
        'release_date',
        'release_amount',
        'release_percentage',
        'remaining_balance',
        'release_condition_met',
        'condition_date',
        'certificate_reference',
        'bank_guarantee_returned',
        'guarantee_return_date',
        'approved_by_id',
        'approved_at',
        'payment_reference',
        'payment_date',
        'status',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'release_date' => 'date',
        'condition_date' => 'date',
        'guarantee_return_date' => 'date',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
        'release_amount' => 'decimal:2',
        'release_percentage' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'bank_guarantee_returned' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->release_number)) {
                $model->release_number = static::generateReleaseNumber();
            }
        });
    }

    protected static function generateReleaseNumber(): string
    {
        $year = date('Y');
        $lastRelease = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastRelease ? intval(substr($lastRelease->release_number, -4)) + 1 : 1;
        
        return sprintf('REL-%s-%04d', $year, $nextNumber);
    }

    public function retention(): BelongsTo
    {
        return $this->belongsTo(Retention::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
