<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number',
        'document_name',
        'document_type',
        'category',
        'related_entity_type',
        'related_entity_id',
        'version',
        'file_path',
        'file_size',
        'file_type',
        'description',
        'tags',
        'is_confidential',
        'uploaded_by_id',
        'status',
        'approved_by_id',
        'approved_at',
        'expiry_date',
        'company_id',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_confidential' => 'boolean',
        'approved_at' => 'datetime',
        'expiry_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->document_number)) {
                $document->document_number = self::generateDocumentNumber();
            }
        });
    }

    public static function generateDocumentNumber(): string
    {
        $year = date('Y');
        $lastDocument = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastDocument ? (int) substr($lastDocument->document_number, -4) + 1 : 1;

        return sprintf('DOC-%s-%04d', $year, $sequence);
    }

    // Relationships
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function accessRights(): HasMany
    {
        return $this->hasMany(DocumentAccess::class);
    }
}
