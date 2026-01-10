<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteDiary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'diary_number',
        'project_id',
        'diary_date',
        'weather_morning',
        'weather_afternoon',
        'temperature_min',
        'temperature_max',
        'humidity',
        'wind_speed',
        'site_condition',
        'work_status',
        'delay_reason',
        'prepared_by_id',
        'reviewed_by_id',
        'approved_by_id',
        'status',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'diary_date' => 'date',
        'temperature_min' => 'decimal:2',
        'temperature_max' => 'decimal:2',
        'humidity' => 'decimal:2',
        'wind_speed' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($diary) {
            if (empty($diary->diary_number)) {
                $diary->diary_number = self::generateDiaryNumber($diary->project_id, $diary->diary_date);
            }
        });
    }

    public static function generateDiaryNumber($projectId, $diaryDate): string
    {
        $date = \Carbon\Carbon::parse($diaryDate);
        $dateStr = $date->format('Ymd');
        
        $count = self::whereDate('diary_date', $date->format('Y-m-d'))
            ->where('project_id', $projectId)
            ->count();
        
        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        return "SD-{$dateStr}-{$sequence}";
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function manpower(): HasMany
    {
        return $this->hasMany(DiaryManpower::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(DiaryEquipment::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DiaryActivity::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(DiaryMaterial::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(DiaryVisitor::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(DiaryIncident::class);
    }

    public function instructions(): HasMany
    {
        return $this->hasMany(DiaryInstruction::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DiaryPhoto::class);
    }

    // Scopes
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('diary_date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Methods
    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'submitted']);
    }

    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function review($userId)
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_by_id' => $userId,
            'reviewed_at' => now(),
        ]);
    }

    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by_id' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'draft',
        ]);
    }
}
