<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractAmendment extends Model
{
    protected $fillable = [
        'contract_id',
        'amendment_number',
        'amendment_code',
        'title',
        'description',
        'amendment_date',
        'effective_date',
        'previous_contract_value',
        'new_contract_value',
        'previous_completion_date',
        'new_completion_date',
        'days_extended',
        'status',
        'approved_by_id',
        'approved_at',
        'attachment_path',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'previous_contract_value' => 'decimal:2',
        'new_contract_value' => 'decimal:2',
        'amendment_date' => 'date',
        'effective_date' => 'date',
        'previous_completion_date' => 'date',
        'new_completion_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['value_difference'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($amendment) {
            // Auto-generate amendment code if not provided
            if (! $amendment->amendment_code) {
                $amendment->amendment_code = static::generateAmendmentCode($amendment->contract_id);
            }

            // Auto-generate amendment number if not provided
            if (! $amendment->amendment_number) {
                $lastAmendment = static::where('contract_id', $amendment->contract_id)
                    ->orderBy('amendment_number', 'desc')
                    ->first();

                $amendment->amendment_number = $lastAmendment ? ($lastAmendment->amendment_number + 1) : 1;
            }
        });
    }

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Accessors
    public function getValueDifferenceAttribute()
    {
        return $this->new_contract_value - $this->previous_contract_value;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Methods
    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by_id' => $userId,
            'approved_at' => now(),
        ]);

        // Update contract with amendment values
        $contract = $this->contract;
        $contract->current_contract_value = $this->new_contract_value;

        if ($this->new_completion_date) {
            $contract->completion_date = $this->new_completion_date;
        }

        $contract->save();
    }

    // Static methods
    public static function generateAmendmentCode($contractId)
    {
        $contract = Contract::find($contractId);
        if (! $contract) {
            return null;
        }

        $contractCode = str_replace('CNT-', '', $contract->contract_code);
        $prefix = "AMD-CNT-{$contractCode}-";

        // Get the last amendment code for this contract
        $lastAmendment = static::where('contract_id', $contractId)
            ->where('amendment_code', 'LIKE', $prefix.'%')
            ->orderBy('amendment_code', 'desc')
            ->first();

        if ($lastAmendment) {
            $lastNumber = intval(substr($lastAmendment->amendment_code, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
