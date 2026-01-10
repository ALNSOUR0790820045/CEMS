<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Check extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'check_number',
        'issue_date',
        'due_date',
        'payment_date',
        'check_type',
        'amount',
        'currency_id',
        'exchange_rate',
        'amount_in_base_currency',
        'amount_words',
        'amount_words_en',
        'beneficiary',
        'bank_account_id',
        'branch_id',
        'description',
        'reference_type',
        'reference_id',
        'project_id',
        'status',
        'template_id',
        'notes',
        'created_by',
        'approved_by',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'exchange_rate' => 'decimal:6',
        'amount_in_base_currency' => 'decimal:3',
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    // Check Types
    const TYPE_CURRENT = 'current';
    const TYPE_POST_DATED = 'post_dated';
    const TYPE_DEFERRED = 'deferred';

    // Check Statuses
    const STATUS_ISSUED = 'issued';
    const STATUS_PENDING = 'pending';
    const STATUS_DUE = 'due';
    const STATUS_CLEARED = 'cleared';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function template()
    {
        return $this->belongsTo(PaymentTemplate::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDue($query)
    {
        return $query->where('status', self::STATUS_DUE);
    }

    public function scopeCleared($query)
    {
        return $query->where('status', self::STATUS_CLEARED);
    }

    public function scopeBounced($query)
    {
        return $query->where('status', self::STATUS_BOUNCED);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_DUE])
            ->where('due_date', '<=', Carbon::now()->addDays($days))
            ->where('due_date', '>=', Carbon::now());
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_DUE])
            ->where('due_date', '<', Carbon::now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('check_type', $type);
    }

    // Accessors
    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->due_date, false);
    }

    public function getIsOverdueAttribute()
    {
        $days = $this->days_until_due;
        return $days !== null && $days < 0 && in_array($this->status, [self::STATUS_PENDING, self::STATUS_DUE]);
    }

    public function getTypeNameAttribute()
    {
        $types = [
            self::TYPE_CURRENT => 'شيك حالي',
            self::TYPE_POST_DATED => 'شيك آجل',
            self::TYPE_DEFERRED => 'شيك مؤجل',
        ];

        return $types[$this->check_type] ?? $this->check_type;
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_ISSUED => 'صادر',
            self::STATUS_PENDING => 'معلق',
            self::STATUS_DUE => 'مستحق',
            self::STATUS_CLEARED => 'تم الصرف',
            self::STATUS_BOUNCED => 'مرتد',
            self::STATUS_CANCELLED => 'ملغى',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Methods
    public static function generateCheckNumber($bankAccountId)
    {
        return \DB::transaction(function () use ($bankAccountId) {
            $year = Carbon::now()->year;
            $lastCheck = self::where('bank_account_id', $bankAccountId)
                ->whereYear('created_at', $year)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            $number = $lastCheck ? ((int) substr($lastCheck->check_number, -6)) + 1 : 1;

            return sprintf('CHK-%d-%06d', $year, $number);
        });
    }

    public function canBeModified()
    {
        return !in_array($this->status, [self::STATUS_CLEARED, self::STATUS_BOUNCED, self::STATUS_CANCELLED]);
    }

    public function canBeDeleted()
    {
        return $this->status !== self::STATUS_CLEARED;
    }
}
