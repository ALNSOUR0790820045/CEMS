<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostPlusTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'cost_plus_contract_id',
        'project_id',
        'transaction_date',
        'cost_type',
        'description',
        'vendor_name',
        'invoice_number',
        'invoice_date',
        'gross_amount',
        'discount',
        'net_amount',
        'currency',
        'has_original_invoice',
        'original_invoice_file',
        'has_payment_receipt',
        'payment_receipt_file',
        'has_grn',
        'grn_id',
        'has_photo_evidence',
        'photo_file',
        'photo_latitude',
        'photo_longitude',
        'photo_timestamp',
        'documentation_complete',
        'is_reimbursable',
        'status',
        'rejection_reason',
        'recorded_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'invoice_date' => 'date',
        'gross_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'has_original_invoice' => 'boolean',
        'has_payment_receipt' => 'boolean',
        'has_grn' => 'boolean',
        'has_photo_evidence' => 'boolean',
        'photo_latitude' => 'decimal:8',
        'photo_longitude' => 'decimal:8',
        'photo_timestamp' => 'datetime',
        'documentation_complete' => 'boolean',
        'is_reimbursable' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function costPlusContract(): BelongsTo
    {
        return $this->belongsTo(CostPlusContract::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function grn(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptNote::class, 'grn_id');
    }

    public function checkDocumentation(): bool
    {
        $this->documentation_complete = 
            $this->has_original_invoice &&
            $this->has_payment_receipt &&
            $this->has_grn &&
            $this->has_photo_evidence;

        $this->save();

        return $this->documentation_complete;
    }

    public function approve(int $userId): bool
    {
        if (!$this->documentation_complete) {
            return false;
        }

        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->status = 'approved';

        return $this->save();
    }
}
