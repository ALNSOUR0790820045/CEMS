<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Project extends Model
{
    use SoftDeletes;

    // Project Status Constants
    const STATUS_PLANNING = 'planning';
    const STATUS_ACTIVE = 'active';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'project_code',
        'project_number',
        'name',
        'name_en',
        'code',
        'description',
        'company_id',
        'department_id',
        'tender_id',
        'contract_id',
        'client_id',
        'client_contract_number',
        'project_type',
        'type',
        'category',
        'project_status',
        'status',
        'health',
        'priority',
        'location',
        'city',
        'city_id',
        'region',
        'country',
        'country_id',
        'site_address',
        'latitude',
        'longitude',
        'gps_latitude',
        'gps_longitude',
        'award_date',
        'contract_date',
        'commencement_date',
        'contract_start_date',
        'contract_end_date',
        'original_completion_date',
        'revised_completion_date',
        'actual_start_date',
        'actual_end_date',
        'actual_completion_date',
        'handover_date',
        'final_handover_date',
        'start_date',
        'end_date',
        'contract_duration_days',
        'original_duration_days',
        'approved_extension_days',
        'contract_value',
        'original_contract_value',
        'approved_variations',
        'revised_contract_value',
        'budget',
        'actual_cost',
        'contract_currency_id',
        'currency',
        'advance_payment_percentage',
        'advance_payment_amount',
        'retention_percentage',
        'performance_bond_percentage',
        'physical_progress',
        'financial_progress',
        'time_progress',
        'project_manager_id',
        'manager_id',
        'site_engineer_id',
        'contract_manager_id',
        'quantity_surveyor_id',
        'created_by',
        'performance_bond_id',
        'advance_bond_id',
        'is_active',
        'is_billable',
        'notes',
    ];

    protected $casts = [
        'award_date' => 'date',
        'contract_date' => 'date',
        'commencement_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'original_completion_date' => 'date',
        'revised_completion_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'actual_completion_date' => 'date',
        'handover_date' => 'date',
        'final_handover_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_duration_days' => 'integer',
        'original_duration_days' => 'integer',
        'approved_extension_days' => 'integer',
        'contract_value' => 'decimal:2',
        'original_contract_value' => 'decimal:2',
        'approved_variations' => 'decimal: 2',
        'revised_contract_value' => 'decimal: 2',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'advance_payment_percentage' => 'decimal:2',
        'advance_payment_amount' => 'decimal:2',
        'retention_percentage' => 'decimal: 2',
        'performance_bond_percentage' => 'decimal: 2',
        'physical_progress' => 'decimal:2',
        'financial_progress' => 'decimal:2',
        'time_progress' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal: 8',
        'is_active' => 'boolean',
        'is_billable' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'contract_currency_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function siteEngineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'site_engineer_id');
    }

    public function contractManager()
    {
        return $this->belongsTo(User::class, 'contract_manager_id');
    }

    public function quantitySurveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'quantity_surveyor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function performanceBond(): BelongsTo
    {
        return $this->belongsTo(Guarantee::class, 'performance_bond_id');
    }

    public function advanceBond(): BelongsTo
    {
        return $this->belongsTo(Guarantee::class, 'advance_bond_id');
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class);
    }

    public function team(): HasMany
    {
        return $this->hasMany(ProjectTeam:: class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function progressReports(): HasMany
    {
        return $this->hasMany(ProjectProgressReport::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(ProjectIssue::class);
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class);
    }

    public function boqItems(): HasMany
    {
        return $this->hasMany(BoqItem::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction:: class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('project_status', $status);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByManager($query, $userId)
    {
        return $query->where('project_manager_id', $userId);
    }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        return $this->physical_progress ??  0;
    }

    public function getDaysRemainingAttribute()
    {
        $endDate = $this->contract_end_date ??  $this->end_date;
        
        if (! $endDate) {
            return null;
        }

        $today = Carbon::now();
        $end = Carbon::parse($endDate);

        if ($end->isPast()) {
            return 0;
        }

        return $today->diffInDays($end);
    }

    public function getContractDurationAttribute()
    {
        return $this->contract_duration_days ?? $this->original_duration_days;
    }

    public function getIsOverdueAttribute()
    {
        $endDate = $this->contract_end_date ?? $this->end_date;
        
        if (!$endDate) {
            return false;
        }

        $isCompleted = in_array($this->project_status ??  $this->status, ['completed', 'closed', 'handed_over', 'final_handover']);

        return Carbon::parse($endDate)->isPast() && !$isCompleted;
    }

    public function getStatusBadgeAttribute()
    {
        $status = $this->project_status ?? $this->status;
        
        return match($status) {
            'not_started' => 'gray',
            'mobilization' => 'blue',
            'in_progress' => 'green',
            'on_hold' => 'yellow',
            'suspended' => 'orange',
            'completed' => 'purple',
            'handed_over' => 'indigo',
            'final_handover' => 'teal',
            'closed' => 'gray',
            'terminated' => 'red',
            'planning' => 'blue',
            'active' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getHealthBadgeAttribute()
    {
        return match($this->health) {
            'on_track' => 'green',
            'at_risk' => 'yellow',
            'delayed' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    // Static methods
    public static function generateProjectCode()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last project code for this year-month
        $lastProject = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastProject && preg_match('/PRJ-(\d{4})-(\d{2})-(\d{4})/', $lastProject->project_code, $matches)) {
            $sequence = intval($matches[3]) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('PRJ-%s-%s-%04d', $year, $month, $sequence);
    }
}