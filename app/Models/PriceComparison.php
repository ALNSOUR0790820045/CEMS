<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceComparison extends Model
{
    protected $fillable = [
        'comparison_number',
        'price_request_id',
        'comparison_date',
        'selected_quotation_id',
        'selection_justification',
        'prepared_by',
        'approved_by',
    ];

    protected $casts = [
        'comparison_date' => 'date',
    ];

    public function priceRequest()
    {
        return $this->belongsTo(PriceRequest::class);
    }

    public function selectedQuotation()
    {
        return $this->belongsTo(PriceQuotation::class, 'selected_quotation_id');
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
