<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeBarEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_number',
        'project_id',
        'contract_id',
        'title',
        'description',
        'event_date',
        'discovery_date',
        'event_type',
        'notice_period_days',
        'notice_deadline',
        'days_remaining',
        'notice_sent',
        'notice_sent_date',
        'notice_reference',
        'notice_correspondence_id',
        'estimated_delay_days',
        'estimated_cost_impact',
        'currency',
        'status',
        'priority',
        'claim_id',
        'variation_order_id',
        'identified_by',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'discovery_date' => 'date',
        'notice_deadline' => 'date',
        'notice_sent_date' => 'date',
        'notice_sent' => 'boolean',
        'notice_period_days' => 'integer',
        'days_remaining' => 'integer',
        'estimated_delay_days' => 'integer',
        'estimated_cost_impact' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->event_number)) {
                $event->event_number = static::generateEventNumber();
            }

            // Calculate notice deadline if not set
            if (empty($event->notice_deadline) && ! empty($event->discovery_date)) {
                $event->notice_deadline = Carbon::parse($event->discovery_date)
                    ->addDays($event->notice_period_days ?? 28);
            }

            // Calculate days remaining
            $event->updateDaysRemaining();
        });

        static::updating(function ($event) {
            $event->updateDaysRemaining();
        });
    }

    public static function generateEventNumber(): string
    {
        $year = date('Y');
        $lastEvent = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastEvent ? intval(substr($lastEvent->event_number, -4)) + 1 : 1;

        return sprintf('TBE-%s-%04d', $year, $number);
    }

    public function updateDaysRemaining(): void
    {
        if ($this->notice_deadline) {
            $this->days_remaining = max(0, Carbon::now()->diffInDays($this->notice_deadline, false));

            // Update status if time barred
            if ($this->days_remaining <= 0 && ! $this->notice_sent && $this->status !== 'time_barred') {
                $this->status = 'time_barred';
            }
        }
    }

    public function isExpired(): bool
    {
        return $this->days_remaining <= 0 && ! $this->notice_sent;
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->days_remaining > 0 && $this->days_remaining <= $days && ! $this->notice_sent;
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function noticeCorrespondence(): BelongsTo
    {
        return $this->belongsTo(Correspondence::class, 'notice_correspondence_id');
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function variationOrder(): BelongsTo
    {
        return $this->belongsTo(VariationOrder::class);
    }

    public function identifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'identified_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(TimeBarAlert::class, 'event_id');
    }

    // Scopes
    public function scopeExpiring($query, int $days = 7)
    {
        return $query->where('notice_sent', false)
            ->where('days_remaining', '>', 0)
            ->where('days_remaining', '<=', $days)
            ->whereNotIn('status', ['time_barred', 'cancelled', 'resolved']);
    }

    public function scopeExpired($query)
    {
        return $query->where('notice_sent', false)
            ->where('days_remaining', '<=', 0)
            ->orWhere('status', 'time_barred');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['time_barred', 'cancelled', 'resolved']);
    }
}
