<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportPhoto extends Model
{
    protected $fillable = [
        'daily_report_id',
        'photo_path',
        'photo_title',
        'description',
        'latitude',
        'longitude',
        'captured_at',
        'device_info',
        'category',
        'activity_id',
        'location_name',
        'hash',
        'verified',
        'uploaded_by',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'verified' => 'boolean',
    ];

    // Relationships
    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper Methods
    public function generateHash(): string
    {
        $dataToHash = $this->photo_path . 
                      $this->latitude . 
                      $this->longitude . 
                      $this->captured_at;
        
        return hash('sha256', $dataToHash);
    }

    public function verifyHash(): bool
    {
        return $this->hash === $this->generateHash();
    }

    // Boot method to auto-generate hash
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($photo) {
            if (empty($photo->hash)) {
                $photo->hash = $photo->generateHash();
                $photo->verified = true;
            }
        });
    }
}
