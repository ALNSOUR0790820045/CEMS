<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractChangeOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'change_order_number',
        'change_order_code',
        'title',
        'description',
        'reason',
        'change_type',
        'financial_impact',
        'value_change',
        'time_impact',
        'days_change',
        'status',
        'submission_date',
        'approval_date',
        'implementation_date',
        'submitted_by_id',
        'approved_by_id',
        'attachment_path',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'value_change' => 'decimal:2',
        'submission_date' => 'date',
        'approval_date' => 'date',
        'implementation_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($changeOrder) {
            // Auto-generate change order code if not provided
            if (! $changeOrder->change_order_code) {
                $changeOrder->change_order_code = static::generateChangeOrderCode($changeOrder->contract_id);
            }

            // Auto-generate change order number if not provided
            if (! $changeOrder->change_order_number) {
                $lastOrder = static::where('contract_id', $changeOrder->contract_id)
                    ->orderBy('change_order_number', 'desc')
                    ->first();

                $nextNumber = $lastOrder ? (intval(substr($lastOrder->change_order_number, 3)) + 1) : 1;
                $changeOrder->change_order_number = 'CO-'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'submitted', 'under_review']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeImplemented($query)
    {
        return $query->where('status', 'implemented');
    }

    // Methods
    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submission_date' => now(),
        ]);
    }

    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approval_date' => now(),
            'approved_by_id' => $userId,
        ]);
    }

    public function reject($userId)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by_id' => $userId,
        ]);
    }

    public function implement()
    {
        if ($this->status !== 'approved') {
            throw new \Exception('Only approved change orders can be implemented');
        }

        // Update contract values
        $contract = $this->contract;
        $contract->current_contract_value += $this->value_change;
        $contract->total_change_orders_value += $this->value_change;

        // Update completion date if there's a time extension
        if ($this->time_impact === 'extension' && $this->days_change > 0) {
            $contract->completion_date = \Carbon\Carbon::parse($contract->completion_date)
                ->addDays($this->days_change);
        } elseif ($this->time_impact === 'reduction' && $this->days_change > 0) {
            $contract->completion_date = \Carbon\Carbon::parse($contract->completion_date)
                ->subDays($this->days_change);
        }

        $contract->save();

        // Update change order status
        $this->update([
            'status' => 'implemented',
            'implementation_date' => now(),
        ]);
    }

    // Static methods
    public static function generateChangeOrderCode($contractId)
    {
        $contract = Contract::find($contractId);
        if (! $contract) {
            return null;
        }

        $contractCode = str_replace('CNT-', '', $contract->contract_code);
        $prefix = "CO-CNT-{$contractCode}-";

        // Get the last change order code for this contract
        $lastChangeOrder = static::where('contract_id', $contractId)
            ->where('change_order_code', 'LIKE', $prefix.'%')
            ->orderBy('change_order_code', 'desc')
            ->first();

        if ($lastChangeOrder) {
            $lastNumber = intval(substr($lastChangeOrder->change_order_code, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
