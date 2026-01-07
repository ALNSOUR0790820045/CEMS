<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationOrderAttachment extends Model
{
    protected $fillable = [
        'variation_order_id',
        'name',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'uploaded_by',
    ];

    public function variationOrder()
    {
        return $this->belongsTo(VariationOrder::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
