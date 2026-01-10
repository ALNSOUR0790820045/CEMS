<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_code',
        'first_name',
        'middle_name',
        'last_name',
        'first_name_en',
        'middle_name_en',
        'last_name_en',
        'national_id',
        'passport_number',
        'date_of_birth',
        'place_of_birth',
        'nationality_id',
        'gender',
        'marital_status',
        'mobile',
        'phone',
        'email',
        'country_id',
        'city_id',
        'address',
        'department_id',
        'position_id',
        'job_title',
        'employee_type',
        'employment_status',
        'hire_date',
        'contract_start_date',
        'contract_end_date',
        'probation_end_date',
        'resignation_date',
        'termination_date',
        'termination_reason',
        'basic_salary',
        'currency_id',
        'payment_frequency',
        'bank_id',
        'bank_account_number',
        'iban',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'visa_number',
        'visa_expiry_date',
        'work_permit_number',
        'work_permit_expiry_date',
        'health_insurance_number',
        'health_insurance_expiry_date',
        'supervisor_id',
        'photo_path',
        'notes',
        'is_active',
        'user_id',
        'company_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'probation_end_date' => 'date',
        'resignation_date' => 'date',
        'termination_date' => 'date',
        'visa_expiry_date' => 'date',
        'work_permit_expiry_date' => 'date',
        'health_insurance_expiry_date' => 'date',
        'basic_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function dependents(): HasMany
    {
        return $this->hasMany(EmployeeDependent::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(EmployeeQualification::class);
    }

    public function workHistory(): HasMany
    {
        return $this->hasMany(EmployeeWorkHistory::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('employment_status', 'active');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('employee_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('employment_status', $status);
    }

    public function scopeExpiringDocuments($query, $days = 30)
    {
        $date = Carbon::now()->addDays($days);
        return $query->where(function ($q) use ($date) {
            $q->where('visa_expiry_date', '<=', $date)
              ->orWhere('work_permit_expiry_date', '<=', $date)
              ->orWhere('health_insurance_expiry_date', '<=', $date);
        });
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);
        return implode(' ', $parts);
    }

    public function getFullNameEnAttribute(): ?string
    {
        if (!$this->first_name_en && !$this->last_name_en) {
            return null;
        }
        
        $parts = array_filter([
            $this->first_name_en,
            $this->middle_name_en,
            $this->last_name_en,
        ]);
        return implode(' ', $parts);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth 
            ? Carbon::parse($this->date_of_birth)->age 
            : null;
    }

    public function getYearsOfServiceAttribute(): float
    {
        return Carbon::parse($this->hire_date)->floatDiffInYears(Carbon::now());
    }

    public function getDaysSinceHireAttribute(): int
    {
        return Carbon::parse($this->hire_date)->diffInDays(Carbon::now());
    }

    // Methods
    public static function generateEmployeeCode(): string
    {
        $year = date('Y');
        $lastEmployee = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEmployee && preg_match('/EMP-' . $year . '-(\d{4})/', $lastEmployee->employee_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('EMP-%s-%04d', $year, $nextNumber);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_code)) {
                $employee->employee_code = self::generateEmployeeCode();
            }
        });
    }
}
