<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'pr_number',
        'pr_date',
        'required_date',
        'requested_by_id',
        'department_id',
        'project_id',
        'priority',
        'status',
        'total_amount',
        'approved_by_id',
        'approved_at',
        'rejection_reason',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'pr_date' => 'date',
        'required_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseRequisition) {
            if (empty($purchaseRequisition->pr_number)) {
                $purchaseRequisition->pr_number = static::generatePRNumber();
            }
        });
    }

    public static function generatePRNumber()
    {
        $year = date('Y');
        $lastPR = static::where('pr_number', 'like', "PR-{$year}-%")
            ->orderBy('pr_number', 'desc')
            ->first();

        if ($lastPR) {
            $lastNumber = (int) substr($lastPR->pr_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "PR-{$year}-{$newNumber}";
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(Employee::class, 'requested_by_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function updateTotalAmount()
    {
        $this->total_amount = $this->items()->sum('estimated_total');
        $this->save();
    }

    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by_id = $userId;
        $this->approved_at = now();
        $this->save();
    }

    public function reject($userId, $reason)
    {
        $this->status = 'rejected';
        $this->approved_by_id = $userId;
        $this->rejection_reason = $reason;
        $this->save();
    }
}
