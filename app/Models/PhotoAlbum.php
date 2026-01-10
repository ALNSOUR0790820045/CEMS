<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoAlbum extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'album_number',
        'project_id',
        'name',
        'name_en',
        'description',
        'album_type',
        'cover_photo_id',
        'photos_count',
        'status',
        'created_by_id',
        'company_id',
    ];

    protected $casts = [
        'photos_count' => 'integer',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function coverPhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'cover_photo_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'album_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Auto-generate album number
    public static function generateAlbumNumber($year = null): string
    {
        $year = $year ?? date('Y');
        $lastAlbum = self::where('album_number', 'like', "ALB-{$year}-%")
            ->orderBy('album_number', 'desc')
            ->first();

        if ($lastAlbum) {
            $lastNumber = (int) substr($lastAlbum->album_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('ALB-%s-%04d', $year, $newNumber);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            if (empty($album->album_number)) {
                $album->album_number = self::generateAlbumNumber();
            }
        });
    }
}
