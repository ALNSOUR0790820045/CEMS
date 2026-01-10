<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'photo_number',
        'project_id',
        'album_id',
        'original_filename',
        'file_path',
        'thumbnail_path',
        'medium_path',
        'file_size',
        'mime_type',
        'width',
        'height',
        'title',
        'description',
        'category',
        'location',
        'work_area',
        'activity_id',
        'boq_item_id',
        'taken_date',
        'taken_time',
        'gps_latitude',
        'gps_longitude',
        'gps_accuracy',
        'gps_address',
        'camera_make',
        'camera_model',
        'orientation',
        'weather_condition',
        'tags',
        'is_featured',
        'is_private',
        'uploaded_by_id',
        'taken_by_id',
        'approved',
        'approved_by_id',
        'approved_at',
        'company_id',
    ];

    protected $casts = [
        'taken_date' => 'date',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_private' => 'boolean',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
        'gps_accuracy' => 'decimal:2',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(PhotoAlbum::class, 'album_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class, 'boq_item_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    public function takenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(PhotoAnnotation::class);
    }

    // Auto-generate photo number
    public static function generatePhotoNumber($year = null): string
    {
        $year = $year ?? date('Y');
        $lastPhoto = self::where('photo_number', 'like', "PHT-{$year}-%")
            ->orderBy('photo_number', 'desc')
            ->first();

        if ($lastPhoto) {
            $lastNumber = (int) substr($lastPhoto->photo_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('PHT-%s-%04d', $year, $newNumber);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($photo) {
            if (empty($photo->photo_number)) {
                $photo->photo_number = self::generatePhotoNumber();
            }
        });
    }
}
