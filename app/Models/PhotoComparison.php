<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoComparison extends Model
{
    protected $fillable = [
        'comparison_number',
        'project_id',
        'name',
        'description',
        'before_photo_id',
        'after_photo_id',
        'comparison_date',
        'location',
        'remarks',
        'created_by_id',
    ];

    protected $casts = [
        'comparison_date' => 'date',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function beforePhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'before_photo_id');
    }

    public function afterPhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'after_photo_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    // Auto-generate comparison number
    public static function generateComparisonNumber($year = null): string
    {
        $year = $year ?? date('Y');
        $lastComparison = self::where('comparison_number', 'like', "CMP-{$year}-%")
            ->orderBy('comparison_number', 'desc')
            ->first();

        if ($lastComparison) {
            $lastNumber = (int) substr($lastComparison->comparison_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('CMP-%s-%04d', $year, $newNumber);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comparison) {
            if (empty($comparison->comparison_number)) {
                $comparison->comparison_number = self::generateComparisonNumber();
            }
        });
    }
}
