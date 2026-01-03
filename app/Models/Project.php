<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_code',
        'name',
        'name_en',
        'client_id',
        'contract_id',
        'project_type',
        'project_status',
        'contract_value',
        'contract_currency_id',
        'contract_start_date',
        'contract_end_date',
        'actual_start_date',
        'actual_end_date',
        'contract_duration_days',
        'location',
        'city_id',
        'country_id',
        'site_address',
        'gps_latitude',
        'gps_longitude',
        'project_manager_id',
        'site_engineer_id',
        'contract_manager_id',
        'description',
        'notes',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'contract_value' => 'decimal:2',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
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

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function siteEngineer()
    {
        return $this->belongsTo(User::class, 'site_engineer_id');
    }

    public function contractManager()
    {
        return $this->belongsTo(User::class, 'contract_manager_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
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
        // To be calculated later based on actual progress data
        return 0;
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->contract_end_date) {
            return null;
        }

        $today = Carbon::now();
        $endDate = Carbon::parse($this->contract_end_date);

        if ($endDate->isPast()) {
            return 0;
        }

        return $today->diffInDays($endDate);
    }

    public function getContractDurationAttribute()
    {
        return $this->contract_duration_days;
    }

    public function getIsOverdueAttribute()
    {
        if (!$this->contract_end_date) {
            return false;
        }

        return Carbon::parse($this->contract_end_date)->isPast() && 
               $this->project_status !== 'completed' && 
               $this->project_status !== 'closed';
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
