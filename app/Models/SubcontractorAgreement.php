<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubcontractorAgreement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agreement_number',
        'agreement_date',
        'subcontractor_id',
        'project_id',
        'contract_id',
        'agreement_type',
        'scope_of_work',
        'contract_value',
        'currency_id',
        'start_date',
        'end_date',
        'retention_percentage',
        'advance_payment_percentage',
        'advance_payment_amount',
        'payment_terms',
        'performance_bond_percentage',
        'performance_bond_amount',
        'status',
        'attachment_path',
        'approved_by_id',
        'approved_at',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_value' => 'decimal:2',
        'retention_percentage' => 'decimal:2',
        'advance_payment_percentage' => 'decimal:2',
        'advance_payment_amount' => 'decimal:2',
        'performance_bond_percentage' => 'decimal:2',
        'performance_bond_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agreement) {
            if (empty($agreement->agreement_number)) {
                $agreement->agreement_number = self::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $lastNumber = self::where('agreement_number', 'like', "SCA-{$year}-%")
            ->orderBy('agreement_number', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->agreement_number, -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return "SCA-{$year}-{$newNum}";
    }

    // Relationships
    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Subcontractor::class);
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(SubcontractorWorkOrder::class);
    }

    public function ipcs(): HasMany
    {
        return $this->hasMany(SubcontractorIpc::class);
    }

    // Accessors
    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
