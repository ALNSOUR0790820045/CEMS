<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteReceiptPhoto extends Model
{
    protected $fillable = [
        'site_receipt_id',
        'photo_path',
        'title',
        'latitude',
        'longitude',
        'captured_at',
        'device_info',
        'hash',
        'verified',
        'category',
        'uploaded_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'captured_at' => 'datetime',
        'verified' => 'boolean',
    ];

    public function siteReceipt(): BelongsTo
    {
        return $this->belongsTo(SiteReceipt::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Generate hash for photo verification
     */
    public static function generateHash(string $photoPath, float $lat, float $lng, string $timestamp): string
    {
        return hash('sha256', $photoPath . $lat . $lng . $timestamp);
    }
}
