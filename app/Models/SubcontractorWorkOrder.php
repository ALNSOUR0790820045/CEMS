<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontractorWorkOrder extends Model
{
    protected $fillable = [
        'work_order_number',
        'work_order_date',
        'subcontractor_agreement_id',
        'subcontractor_id',
        'project_id',
        'work_description',
        'location',
        'order_value',
        'currency_id',
        'start_date',
        'end_date',
        'status',
        'approved_by_id',
        'approved_at',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'work_order_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'order_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workOrder) {
            if (empty($workOrder->work_order_number)) {
                $workOrder->work_order_number = self::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $lastNumber = self::where('work_order_number', 'like', "SWO-{$year}-%")
            ->orderBy('work_order_number', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->work_order_number, -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return "SWO-{$year}-{$newNum}";
    }

    // Relationships
    public function agreement(): BelongsTo
    {
        return $this->belongsTo(SubcontractorAgreement::class, 'subcontractor_agreement_id');
    }

    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
