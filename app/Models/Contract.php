<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_code',
        'contract_number',
        'contract_title',
        'contract_title_en',
        'name',
        'name_en',
        'code',
        'title',
        'description',
        'client_id',
        'project_id',
        'tender_id',
        'contract_type',
        'contract_category',
        'type',
        'contract_value',
        'value',
        'amount',
        'currency',
        'currency_id',
        'signing_date',
        'contract_date',
        'signed_date',
        'commencement_date',
        'start_date',
        'completion_date',
        'end_date',
        'contract_duration_days',
        'duration_days',
        'defects_liability_period',
        'retention_percentage',
        'advance_payment_percentage',
        'payment_terms',
        'penalty_clause',
        'scope_of_work',
        'special_conditions',
        'contract_status',
        'status',
        'original_contract_value',
        'current_contract_value',
        'total_change_orders_value',
        'contract_manager_id',
        'project_manager_id',
        'gl_revenue_account_id',
        'gl_receivable_account_id',
        'parent_contract_id',
        'attachment_path',
        'notes',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'value' => 'decimal:2',
        'amount' => 'decimal:2',
        'original_contract_value' => 'decimal:2',
        'current_contract_value' => 'decimal:2',
        'total_change_orders_value' => 'decimal:2',
        'retention_percentage' => 'decimal: 2',
        'advance_payment_percentage' => 'decimal: 2',
        'signing_date' => 'date',
        'contract_date' => 'date',
        'signed_date' => 'date',
        'commencement_date' => 'date',
        'start_date' => 'date',
        'completion_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (! $contract->contract_code) {
                $contract->contract_code = static::generateContractCode();
            }

            if (! $contract->original_contract_value) {
                $contract->original_contract_value = $contract->contract_value ?? $contract->value ?? $contract->amount;
            }
            if (! $contract->current_contract_value) {
                $contract->current_contract_value = $contract->contract_value ?? $contract->value ?? $contract->amount;
            }

            $startDate = $contract->commencement_date ?? $contract->start_date;
            $endDate = $contract->completion_date ?? $contract->end_date;

            if ($startDate && $endDate) {
                $contract->contract_duration_days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                $contract->duration_days = $contract->contract_duration_days;
            }
        });

        static::updating(function ($contract) {
            if ($contract->isDirty(['commencement_date', 'completion_date', 'start_date', 'end_date'])) {
                $startDate = $contract->commencement_date ?? $contract->start_date;
                $endDate = $contract->completion_date ?? $contract->end_date;

                if ($startDate && $endDate) {
                    $contract->contract_duration_days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                    $contract->duration_days = $contract->contract_duration_days;
                }
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function contractManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contract_manager_id');
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function glRevenueAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'gl_revenue_account_id');
    }

    public function glReceivableAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'gl_receivable_account_id');
    }

    public function parentContract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'parent_contract_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function changeOrders(): HasMany
    {
        return $this->hasMany(ContractChangeOrder::class);
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(ContractAmendment::class);
    }

    public function clauses(): HasMany
    {
        return $this->hasMany(ContractClause::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ContractMilestone::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class);
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function ipcs(): HasMany
    {
        return $this->hasMany(IPC::class);
    }

    public function arInvoices(): HasMany
    {
        return $this->hasMany(ARInvoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('is_active', true)
                ->orWhere('status', 'active')
                ->orWhere('contract_status', 'active');
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where(function ($q) use ($status) {
            $q->where('contract_status', $status)
                ->orWhere('status', $status);
        });
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where(function ($q) use ($type) {
            $q->where('contract_type', $type)
                ->orWhere('type', $type);
        });
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        $futureDate = Carbon::now()->addDays($days);

        return $query->where(function ($q) use ($futureDate) {
            $q->where('completion_date', '<=', $futureDate)
                ->where('completion_date', '>=', Carbon::now())
                ->orWhere(function ($q2) use ($futureDate) {
                    $q2->where('end_date', '<=', $futureDate)
                        ->where('end_date', '>=', Carbon::now());
                });
        })->where(function ($q) {
            $q->whereIn('contract_status', ['active', 'signed'])
                ->orWhere('status', 'active');
        });
    }

    public function getDaysRemainingAttribute()
    {
        $completionDate = $this->completion_date ?? $this->end_date;

        if (! $completionDate) {
            return null;
        }

        $now = Carbon::now();
        $endDate = Carbon::parse($completionDate);

        if ($endDate->isPast()) {
            return 0;
        }

        return $now->diffInDays($endDate);
    }

    public function getProgressPercentageAttribute()
    {
        $duration = $this->contract_duration_days ?? $this->duration_days;

        if (! $duration || $duration == 0) {
            return 0;
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($this->commencement_date ?? $this->start_date);
        $endDate = Carbon::parse($this->completion_date ?? $this->end_date);

        if ($now->isBefore($startDate)) {
            return 0;
        }

        if ($now->isAfter($endDate)) {
            return 100;
        }

        $daysPassed = $startDate->diffInDays($now);

        return round(($daysPassed / $duration) * 100, 2);
    }

    public function getIsExpiredAttribute()
    {
        $completionDate = $this->completion_date ?? $this->end_date;

        if (! $completionDate) {
            return false;
        }

        return Carbon::parse($completionDate)->isPast();
    }

    public function getIsNearExpiryAttribute()
    {
        $completionDate = $this->completion_date ?? $this->end_date;

        if (! $completionDate) {
            return false;
        }

        $endDate = Carbon::parse($completionDate);
        $now = Carbon::now();

        return $endDate->isFuture() && $endDate->diffInDays($now) <= 30;
    }

    public static function generateContractCode()
    {
        $year = date('Y');
        $prefix = "CNT-{$year}-";

        $lastContract = static::where('contract_code', 'LIKE', $prefix.'%')
            ->orderBy('contract_code', 'desc')
            ->first();

        if ($lastContract) {
            $lastNumber = intval(substr($lastContract->contract_code, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
