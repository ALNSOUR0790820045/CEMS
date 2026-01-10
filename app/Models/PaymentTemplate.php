<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'type',
        'category',
        'content',
        'variables',
        'styles',
        'is_default',
        'company_id',
        'branch_id',
        'bank_id',
        'language',
        'paper_size',
        'orientation',
        'margins',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'margins' => 'array',
        'is_default' => 'boolean',
    ];

    // Template Types
    const TYPE_CHECK = 'check';
    const TYPE_PROMISSORY_NOTE = 'promissory_note';
    const TYPE_GUARANTEE = 'guarantee';
    const TYPE_RECEIPT = 'receipt';

    // Template Categories (for guarantees)
    const CATEGORY_ADVANCE_PAYMENT = 'advance_payment';
    const CATEGORY_PERFORMANCE = 'performance';
    const CATEGORY_RETENTION = 'retention';
    const CATEGORY_BID_BOND = 'bid_bond';
    const CATEGORY_GENERAL = 'general';

    // Statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DRAFT = 'draft';

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function checks()
    {
        return $this->hasMany(Check::class, 'template_id');
    }

    public function promissoryNotes()
    {
        return $this->hasMany(PromissoryNote::class, 'template_id');
    }

    public function guarantees()
    {
        return $this->hasMany(Guarantee::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        $types = [
            self::TYPE_CHECK => 'شيك',
            self::TYPE_PROMISSORY_NOTE => 'كمبيالة',
            self::TYPE_GUARANTEE => 'كفالة',
            self::TYPE_RECEIPT => 'إيصال',
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getCategoryNameAttribute()
    {
        $categories = [
            self::CATEGORY_ADVANCE_PAYMENT => 'دفعة مقدمة',
            self::CATEGORY_PERFORMANCE => 'حسن تنفيذ',
            self::CATEGORY_RETENTION => 'استبقاء',
            self::CATEGORY_BID_BOND => 'ضمان ابتدائي',
            self::CATEGORY_GENERAL => 'عام',
        ];

        return $categories[$this->category] ?? $this->category;
    }

    // Methods
    public function render($data = [])
    {
        $content = $this->content;

        // Replace variables in content
        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        return $content;
    }

    public function duplicate()
    {
        $newTemplate = $this->replicate();
        $newTemplate->name = $this->name . ' (نسخة)';
        $newTemplate->name_en = $this->name_en . ' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->status = self::STATUS_DRAFT;
        $newTemplate->save();

        return $newTemplate;
    }
}
