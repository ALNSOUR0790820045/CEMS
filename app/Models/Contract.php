<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_code',
        'contract_number',
        'contract_title',
        'contract_title_en',
        'client_id',
        'contract_type',
        'contract_category',
        'contract_value',
        'currency_id',
        'signing_date',
        'commencement_date',
        'completion_date',
        'contract_duration_days',
        'defects_liability_period',
        'retention_percentage',
        'advance_payment_percentage',
        'payment_terms',
        'penalty_clause',
        'scope_of_work',
        'special_conditions',
        'contract_status',
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
        'original_contract_value' => 'decimal:2',
        'current_contract_value' => 'decimal:2',
        'total_change_orders_value' => 'decimal:2',
        'retention_percentage' => 'decimal:2',
        'advance_payment_percentage' => 'decimal:2',
        'signing_date' => 'date',
        'commencement_date' => 'date',
        'completion_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Boot method to handle automatic values
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            // Auto-generate contract code if not provided
            if (!$contract->contract_code) {
                $contract->contract_code = static::generateContractCode();
            }

            // Set original and current values on creation
            if (!$contract->original_contract_value) {
                $contract->original_contract_value = $contract->contract_value;
            }
            if (!$contract->current_contract_value) {
                $contract->current_contract_value = $contract->contract_value;
            }

            // Calculate contract duration
            if ($contract->commencement_date && $contract->completion_date) {
                $contract->contract_duration_days = Carbon::parse($contract->commencement_date)
                    ->diffInDays(Carbon::parse($contract->completion_date));
            }
        });

        static::updating(function ($contract) {
            // Recalculate contract duration if dates change
            if ($contract->isDirty(['commencement_date', 'completion_date'])) {
                $contract->contract_duration_days = Carbon::parse($contract->commencement_date)
                    ->diffInDays(Carbon::parse($contract->completion_date));
            }
        });
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('contract_status', $status);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('contract_type', $type);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        $futureDate = Carbon::now()->addDays($days);
        return $query->where('completion_date', '<=', $futureDate)
            ->where('completion_date', '>=', Carbon::now())
            ->whereIn('contract_status', ['active', 'signed']);
    }

    // Accessors
    public function getDaysRemainingAttribute()
    {
        if (!$this->completion_date) {
            return null;
        }
        
        $now = Carbon::now();
        $completionDate = Carbon::parse($this->completion_date);
        
        if ($completionDate->isPast()) {
            return 0;
        }
        
        return $now->diffInDays($completionDate);
    }

    public function getProgressPercentageAttribute()
    {
        if (!$this->contract_duration_days || $this->contract_duration_days == 0) {
            return 0;
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($this->commencement_date);
        $endDate = Carbon::parse($this->completion_date);

        if ($now->isBefore($startDate)) {
            return 0;
        }

        if ($now->isAfter($endDate)) {
            return 100;
        }

        $daysPassed = $startDate->diffInDays($now);
        return round(($daysPassed / $this->contract_duration_days) * 100, 2);
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->completion_date) {
            return false;
        }
        
        return Carbon::parse($this->completion_date)->isPast();
    }

    public function getIsNearExpiryAttribute()
    {
        if (!$this->completion_date) {
            return false;
        }
        
        $completionDate = Carbon::parse($this->completion_date);
        $now = Carbon::now();
        
        return $completionDate->isFuture() && $completionDate->diffInDays($now) <= 30;
    }

    // Static methods
    public static function generateContractCode()
    {
        $year = date('Y');
        $prefix = "CNT-{$year}-";
        
        // Get the last contract code for this year
        $lastContract = static::where('contract_code', 'LIKE', $prefix . '%')
            ->orderBy('contract_code', 'desc')
            ->first();
        
        if ($lastContract) {
            $lastNumber = intval(substr($lastContract->contract_code, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
