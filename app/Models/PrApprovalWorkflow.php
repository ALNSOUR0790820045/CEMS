<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrApprovalWorkflow extends Model
{
    protected $fillable = [
        'purchase_requisition_id',
        'approval_level',
        'approver_id',
        'status',
        'approved_at',
        'comments',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Methods
    public function approve(string $comments = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'comments' => $comments,
        ]);
    }

    public function reject(string $comments)
    {
        $this->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'comments' => $comments,
        ]);
    }
}
