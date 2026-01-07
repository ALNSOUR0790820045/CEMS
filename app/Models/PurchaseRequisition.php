<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequisition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'requisition_number',
        'requisition_date',
        'required_date',
        'project_id',
        'department_id',
        'requested_by_id',
        'priority',
        'type',
        'status',
        'total_estimated_amount',
        'currency_id',
        'justification',
        'approved_by_id',
        'approved_at',
        'rejection_reason',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'requisition_date' => 'date',
        'required_date' => 'date',
        'total_estimated_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Boot method for auto-numbering
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($requisition) {
            if (empty($requisition->requisition_number)) {
                $year = date('Y');
                $lastRequisition = static::where('requisition_number', 'like', "PR-{$year}-%")
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastRequisition) {
                    $lastNumber = intval(substr($lastRequisition->requisition_number, -4));
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $requisition->requisition_number = "PR-{$year}-{$newNumber}";
            }
        });
    }

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function approvalWorkflows()
    {
        return $this->hasMany(PrApprovalWorkflow::class);
    }

    public function quotes()
    {
        return $this->hasMany(PrQuote::class);
    }

    // Methods
    public function submit()
    {
        $this->update(['status' => 'pending_approval']);
    }

    public function approve(User $user)
    {
        $this->update([
            'status' => 'approved',
            'approved_by_id' => $user->id,
            'approved_at' => now(),
        ]);
    }

    public function reject(User $user, string $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by_id' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}
