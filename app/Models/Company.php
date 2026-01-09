<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'commercial_registration',
        'tax_number',
        'logo',
        'is_active',
        'established_date',
        'license_number',
        'license_expiry',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'established_date' => 'date',
        'license_expiry' => 'date',
        'settings' => 'array',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }

    public function tenderActivities()
    {
        return $this->hasMany(TenderActivity::class);
    }

    public function payrollPeriods()
    {
        return $this->hasMany(PayrollPeriod::class);
    }

    public function payrollEntries()
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function employeeLoans()
    {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }
}
