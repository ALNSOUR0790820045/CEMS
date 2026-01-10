<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_number',
        'project_id',
        'inspection_type_id',
        'requested_by_id',
        'request_date',
        'requested_date',
        'requested_time',
        'location',
        'work_area',
        'activity_id',
        'boq_item_id',
        'description',
        'priority',
        'status',
        'rejection_reason',
        'scheduled_date',
        'scheduled_time',
        'inspector_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'requested_date' => 'date',
        'scheduled_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function inspectionType(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inspection(): HasOne
    {
        return $this->hasOne(Inspection::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class, 'boq_item_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = static::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber(): string
    {
        $year = date('Y');
        $lastRequest = static::where('request_number', 'like', "IR-{$year}-%")
            ->latest('id')
            ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->request_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "IR-{$year}-{$newNumber}";
    }
}
