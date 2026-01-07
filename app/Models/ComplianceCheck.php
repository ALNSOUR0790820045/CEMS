<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ComplianceCheck extends Model
{
    protected $fillable = [
        'check_number',
        'compliance_requirement_id',
        'project_id',
        'check_date',
        'due_date',
        'status',
        'checked_by_id',
        'findings',
        'corrective_action',
        'evidence_path',
        'next_check_date',
        'company_id',
    ];

    protected $casts = [
        'check_date' => 'date',
        'due_date' => 'date',
        'next_check_date' => 'date',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function complianceRequirement(): BelongsTo
    {
        return $this->belongsTo(ComplianceRequirement::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<', Carbon::now());
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<=', Carbon::now()->addDays($days))
            ->where('due_date', '>=', Carbon::now());
    }

    // Accessors
    public function getStatusNameAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'passed' => 'نجح',
            'failed' => 'فشل',
            'waived' => 'معفى',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->due_date < Carbon::now();
    }

    // Methods
    public static function generateCheckNumber()
    {
        return \DB::transaction(function () {
            $year = Carbon::now()->year;
            $lastCheck = self::whereYear('created_at', $year)
                ->latest('id')
                ->lockForUpdate()
                ->first();
            
            $number = $lastCheck ? ((int) substr($lastCheck->check_number, -4)) + 1 : 1;
            
            return sprintf('CC-%d-%04d', $year, $number);
        });
    }

    public function markAsPassed($checkedBy, $findings = null)
    {
        $this->update([
            'status' => 'passed',
            'checked_by_id' => $checkedBy,
            'check_date' => Carbon::now(),
            'findings' => $findings,
        ]);
    }

    public function markAsFailed($checkedBy, $findings, $correctiveAction = null)
    {
        $this->update([
            'status' => 'failed',
            'checked_by_id' => $checkedBy,
            'check_date' => Carbon::now(),
            'findings' => $findings,
            'corrective_action' => $correctiveAction,
        ]);
    }
}
