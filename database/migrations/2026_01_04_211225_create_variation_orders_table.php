<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variation_orders', function (Blueprint $table) {
            $table->id();
            $table->string('vo_number')->unique(); // VO-PRJ001-001
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained();
            $table->integer('sequence_number'); // الرقم التسلسلي في المشروع
            
            // التفاصيل
            $table->string('title');
            $table->text('description');
            $table->text('justification')->nullable(); // مبررات التغيير
            $table->enum('type', [
                'addition',      // إضافة أعمال
                'omission',      // حذف أعمال
                'modification',  // تعديل أعمال
                'substitution'   // استبدال
            ])->default('addition');
            
            $table->enum('source', [
                'client',        // طلب العميل
                'consultant',    // طلب الاستشاري
                'contractor',    // طلب المقاول
                'design_change', // تغيير التصميم
                'site_condition' // ظروف الموقع
            ])->default('client');
            
            // القيم المالية
            $table->decimal('estimated_value', 18, 2)->default(0);
            $table->decimal('quoted_value', 18, 2)->default(0);
            $table->decimal('approved_value', 18, 2)->default(0);
            $table->decimal('executed_value', 18, 2)->default(0);
            $table->string('currency', 3)->default('SAR');
            
            // التأثير على المدة
            $table->integer('time_impact_days')->default(0);
            $table->boolean('extension_approved')->default(false);
            $table->integer('approved_extension_days')->default(0);
            
            // التواريخ
            $table->date('identification_date'); // تاريخ اكتشاف التغيير
            $table->date('submission_date')->nullable();
            $table->date('client_response_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('execution_start_date')->nullable();
            $table->date('execution_end_date')->nullable();
            
            // الحالة
            $table->enum('status', [
                'identified',        // تم تحديده
                'draft',             // مسودة
                'submitted',         // مقدم
                'under_review',      // قيد المراجعة
                'negotiating',       // قيد التفاوض
                'approved',          // معتمد
                'rejected',          // مرفوض
                'partially_approved', // معتمد جزئياً
                'in_progress',       // قيد التنفيذ
                'completed',         // منتهي
                'cancelled'          // ملغي
            ])->default('identified');
            
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // المراجعة
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('prepared_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_orders');
    }
};
