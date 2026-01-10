<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guarantee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'guarantee_number',
        'type',
        'guarantee_type',
        'project_id',
        'tender_id',
        'contract_id',
        'contract_number',
        'bank_id',
        'bank_name',
        'beneficiary',
        'beneficiary_name',
        'beneficiary_address',
        'amount',
        'currency',
        'currency_id',
        'exchange_rate',
        'amount_in_base_currency',
        'amount_words',
        'amount_words_en',
        'contractor_name',
        'contractor_cr',
        'contractor_address',
        'issue_date',
        'start_date',
        'expiry_date',
        'end_date',
        'expected_release_date',
        'status',
        'bank_charges',
        'bank_commission_rate',
        'cash_margin',
        'margin_percentage',
        'bank_reference_number',
        'lg_number',
        'purpose',
        'description',
        'notes',
        'auto_renewal',
        'renewal_period_days',
        'alert_days_before_expiry',
        'branch_id',
        'template_id',
        'created_by',
        'approved_by',
        'approved_at',
        'released_at',
        'released_by',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'amount_in_base_currency' => 'decimal:3',
        'exchange_rate' => 'decimal:6',
        'bank_charges' => 'decimal:2',
        'bank_commission_rate' => 'decimal:2',
        'cash_margin' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'issue_date' => 'date',
        'start_date' => 'date',
        'expiry_date' => 'date',
        'end_date' => 'date',
        'expected_release_date' => 'date',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
        'auto_renewal' => 'boolean',
    ];

    // Relationships
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function template()
    {
        return $this->belongsTo(PaymentTemplate::class, 'template_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function releaser()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function renewals()
    {
        return $this->hasMany(GuaranteeRenewal::class);
    }

    public function releases()
    {
        return $this->hasMany(GuaranteeRelease::class);
    }

    public function claims()
    {
        return $this->hasMany(GuaranteeClaim::class);
    }

    public function performanceBondProjects()
    {
        return $this->hasMany(Project::class, 'performance_bond_id');
    }

    public function advanceBondProjects()
    {
        return $this->hasMany(Project::class, 'advance_bond_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('status', 'active')
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>=', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getDaysUntilExpiryAttribute()
    {
        if (! $this->expiry_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringAttribute()
    {
        $days = $this->days_until_expiry;
        $alertDays = $this->alert_days_before_expiry ?? 30;

        return $days !== null && $days <= $alertDays && $days > 0;
    }

    public function getIsExpiredAttribute()
    {
        $days = $this->days_until_expiry;

        return $days !== null && $days < 0;
    }

    public function getTypeNameAttribute()
    {
        $types = [
            'bid' => 'ضمان ابتدائي',
            'performance' => 'ضمان حسن التنفيذ',
            'advance_payment' => 'ضمان الدفعة المقدمة',
            'maintenance' => 'ضمان الصيانة',
            'retention' => 'ضمان الاحتجاز',
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            'draft' => 'مسودة',
            'active' => 'نشط',
            'expired' => 'منتهي',
            'released' => 'محرر',
            'claimed' => 'مطالب به',
            'renewed' => 'مجدد',
            'cancelled' => 'ملغي',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Methods
    public static function generateGuaranteeNumber()
    {
        return \DB::transaction(function () {
            $year = Carbon::now()->year;
            $lastGuarantee = self::whereYear('created_at', $year)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            $number = $lastGuarantee ? ((int) substr($lastGuarantee->guarantee_number, -4)) + 1 : 1;

            return sprintf('LG-%d-%04d', $year, $number);
        });
    }

    public function calculateCommission()
    {
        if ($this->bank_commission_rate > 0 && $this->amount > 0) {
            $daysInYear = 365;
            $issueDate = Carbon::parse($this->issue_date);
            $expiryDate = Carbon::parse($this->expiry_date);
            $daysBetween = $issueDate->diffInDays($expiryDate);

            return ($this->amount * $this->bank_commission_rate / 100) * ($daysBetween / $daysInYear);
        }

        return 0;
    }
}
