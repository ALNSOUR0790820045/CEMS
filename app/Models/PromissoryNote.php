<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PromissoryNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'note_number',
        'issue_date',
        'maturity_date',
        'payment_date',
        'amount',
        'currency_id',
        'exchange_rate',
        'amount_in_base_currency',
        'amount_words',
        'amount_words_en',
        'issuer_name',
        'issuer_cr',
        'issuer_address',
        'payee_name',
        'payee_address',
        'place_of_issue',
        'purpose',
        'reference_type',
        'reference_id',
        'project_id',
        'branch_id',
        'status',
        'template_id',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'exchange_rate' => 'decimal:6',
        'amount_in_base_currency' => 'decimal:3',
        'issue_date' => 'date',
        'maturity_date' => 'date',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Statuses
    const STATUS_ISSUED = 'issued';
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_DISHONORED = 'dishonored';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereIn('status', [self::STATUS_ISSUED, self::STATUS_PENDING])
            ->where('maturity_date', '<=', Carbon::now()->addDays($days))
            ->where('maturity_date', '>=', Carbon::now());
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [self::STATUS_ISSUED, self::STATUS_PENDING])
            ->where('maturity_date', '<', Carbon::now());
    }

    // Accessors
    public function getDaysUntilMaturityAttribute()
    {
        if (!$this->maturity_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->maturity_date, false);
    }

    public function getIsOverdueAttribute()
    {
        $days = $this->days_until_maturity;
        return $days !== null && $days < 0 && in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PENDING]);
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_ISSUED => 'صادر',
            self::STATUS_PENDING => 'معلق',
            self::STATUS_PAID => 'مدفوع',
            self::STATUS_DISHONORED => 'مرفوض',
            self::STATUS_CANCELLED => 'ملغى',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Methods
    public static function generateNoteNumber()
    {
        return \DB::transaction(function () {
            $year = Carbon::now()->year;
            $lastNote = self::whereYear('created_at', $year)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            $number = $lastNote ? ((int) substr($lastNote->note_number, -6)) + 1 : 1;

            return sprintf('PN-%d-%06d', $year, $number);
        });
    }

    public function canBeModified()
    {
        return !in_array($this->status, [self::STATUS_PAID, self::STATUS_CANCELLED]);
    }

    public function canBeDeleted()
    {
        return $this->status !== self::STATUS_PAID;
    }
}
