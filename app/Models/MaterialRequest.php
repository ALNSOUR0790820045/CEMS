<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    protected $fillable = [
        'request_number',
        'request_date',
        'required_date',
        'requested_by_id',
        'department_id',
        'project_id',
        'request_type',
        'priority',
        'status',
        'approved_by_id',
        'approved_at',
        'rejection_reason',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber()
    {
        $year = date('Y');
        $lastRequest = self::where('request_number', 'like', "MRQ-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->request_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "MRQ-{$year}-{$newNumber}";
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(Employee::class, 'requested_by_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items()
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by_id' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function reject($reason, $userId)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by_id' => $userId,
            'approved_at' => now(),
        ]);
    }
}
