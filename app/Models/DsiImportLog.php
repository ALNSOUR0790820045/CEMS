<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DsiImportLog extends Model
{
    protected $fillable = [
        'import_date',
        'records_imported',
        'file_path',
        'imported_by',
        'notes',
    ];

    protected $casts = [
        'import_date' => 'date',
        'records_imported' => 'integer',
    ];

    // Relationships
    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
