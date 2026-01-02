<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ChangeOrder extends Model
{
    protected $fillable = [
        'project_id',
        'co_number',
        'issue_date',
        'tender_id',
        'original_contract_id',
        'type',
        'reason',
        'title',
        'description',
        'justification',
        'original_contract_value',
        'net_amount',
        'tax_amount',
        'total_amount',
        'fee_percentage',
        'calculated_fee',
        'stamp_duty',
        'total_fees',
        'time_extension_days',
        'new_completion_date',
        'schedule_impact_description',
        'status',
        'pm_user_id',
        'pm_signed_at',
        'pm_decision',
        'pm_comments',
        'technical_user_id',
        'technical_signed_at',
        'technical_decision',
        'technical_comments',
        'consultant_user_id',
        'consultant_signed_at',
        'consultant_decision',
        'consultant_comments',
        'client_user_id',
        'client_signed_at',
        'client_decision',
        'client_comments',
        'attachments',
        'updated_contract_value',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'new_completion_date' => 'date',
        'original_contract_value' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'fee_percentage' => 'decimal:3',
        'calculated_fee' => 'decimal:2',
        'stamp_duty' => 'decimal:2',
        'total_fees' => 'decimal:2',
        'updated_contract_value' => 'decimal:2',
        'time_extension_days' => 'integer',
        'pm_signed_at' => 'datetime',
        'technical_signed_at' => 'datetime',
        'consultant_signed_at' => 'datetime',
        'client_signed_at' => 'datetime',
        'attachments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($changeOrder) {
            $changeOrder->calculateFees();
            $changeOrder->calculateUpdatedContractValue();
            $changeOrder->calculateNewCompletionDate();
        });
    }

    /**
     * Calculate fees and stamp duty
     */
    public function calculateFees(): void
    {
        // Calculate fee based on percentage of net amount
        $this->calculated_fee = abs($this->net_amount) * ($this->fee_percentage / 100);
        
        // Calculate stamp duty (simplified calculation)
        $this->stamp_duty = $this->calculateStampDuty($this->total_amount);
        
        // Total fees
        $this->total_fees = $this->calculated_fee + $this->stamp_duty;
    }

    /**
     * Calculate stamp duty based on total amount
     * This is a simplified calculation - adjust based on actual legal requirements
     */
    protected function calculateStampDuty(float $amount): float
    {
        // Example calculation: 0.1% of total amount with minimum and maximum
        $duty = abs($amount) * 0.001;
        $min = 50; // Minimum duty
        $max = 10000; // Maximum duty
        
        return min(max($duty, $min), $max);
    }

    /**
     * Calculate updated contract value
     */
    public function calculateUpdatedContractValue(): void
    {
        $this->updated_contract_value = $this->original_contract_value + $this->total_amount;
    }

    /**
     * Calculate new completion date if time extension is applied
     */
    public function calculateNewCompletionDate(): void
    {
        if ($this->time_extension_days > 0 && $this->project) {
            $currentDate = $this->project->completion_date ?? now();
            $this->new_completion_date = Carbon::parse($currentDate)->addDays($this->time_extension_days);
        }
    }

    /**
     * Generate next CO number
     */
    public static function generateCoNumber(): string
    {
        $year = date('Y');
        $lastCo = static::where('co_number', 'like', "CO-{$year}-%")->latest('id')->first();
        
        if ($lastCo) {
            $lastNumber = (int) substr($lastCo->co_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "CO-{$year}-{$newNumber}";
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'original_contract_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChangeOrderItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pmUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_user_id');
    }

    public function technicalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technical_user_id');
    }

    public function consultantUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_user_id');
    }

    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending_pm' => 'blue',
            'pending_technical' => 'yellow',
            'pending_consultant' => 'purple',
            'pending_client' => 'orange',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'pending_pm' => 'بانتظار مدير المشروع',
            'pending_technical' => 'بانتظار المدير الفني',
            'pending_consultant' => 'بانتظار الاستشاري',
            'pending_client' => 'بانتظار العميل',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي',
            default => 'غير معروف',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'scope_change' => 'تغيير في النطاق',
            'quantity_change' => 'تغيير في الكميات',
            'design_change' => 'تغيير في التصميم',
            'specification_change' => 'تغيير في المواصفات',
            'other' => 'أخرى',
            default => 'غير محدد',
        };
    }

    /**
     * Get reason label
     */
    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'client_request' => 'طلب العميل',
            'design_error' => 'خطأ تصميم',
            'site_condition' => 'ظروف الموقع',
            'regulatory' => 'متطلبات تنظيمية',
            'other' => 'أخرى',
            default => 'غير محدد',
        };
    }
}
