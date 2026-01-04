<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Guarantee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'guarantee_number',
        'type',
        'project_id',
        'tender_id',
        'contract_id',
        'bank_id',
        'beneficiary',
        'beneficiary_address',
        'amount',
        'currency',
        'amount_in_base_currency',
        'issue_date',
        'expiry_date',
        'expected_release_date',
        'status',
        'bank_charges',
        'bank_commission_rate',
        'cash_margin',
        'margin_percentage',
        'bank_reference_number',
        'purpose',
        'notes',
        'auto_renewal',
        'renewal_period_days',
        'alert_days_before_expiry',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_in_base_currency' => 'decimal:2',
        'bank_charges' => 'decimal:2',
        'bank_commission_rate' => 'decimal:2',
        'cash_margin' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'expected_release_date' => 'date',
        'approved_at' => 'datetime',
        'auto_renewal' => 'boolean',
    ];

    // Relationships
    public function bank()
    {
        return $this->belongsTo(Bank::class);
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
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringAttribute()
    {
        return $this->days_until_expiry <= $this->alert_days_before_expiry && $this->days_until_expiry > 0;
    }

    public function getIsExpiredAttribute()
    {
        return $this->days_until_expiry < 0;
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
        $year = Carbon::now()->year;
        $lastGuarantee = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastGuarantee ? ((int) substr($lastGuarantee->guarantee_number, -4)) + 1 : 1;
        
        return sprintf('LG-%d-%04d', $year, $number);
    }

    public function calculateCommission()
    {
        if ($this->bank_commission_rate > 0) {
            $daysInYear = 365;
            $daysBetween = Carbon::parse($this->issue_date)->diffInDays(Carbon::parse($this->expiry_date));
            return ($this->amount * $this->bank_commission_rate / 100) * ($daysBetween / $daysInYear);
        }
        
        return 0;
    }
}
