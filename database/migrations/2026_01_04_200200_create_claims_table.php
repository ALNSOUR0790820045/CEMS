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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique(); // CLM-PRJ001-001
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained();
            $table->integer('sequence_number');
            
            // التفاصيل
            $table->string('title');
            $table->text('description');
            $table->text('contractual_basis')->nullable(); // الأساس التعاقدي
            $table->text('facts')->nullable(); // الوقائع
            
            // النوع
            $table->enum('type', [
                'time_extension',      // تمديد وقت
                'cost_compensation',   // تعويض مالي
                'time_and_cost',       // وقت ومال
                'acceleration',        // تسريع
                'disruption',          // إعاقة
                'prolongation',        // إطالة
                'loss_of_productivity' // فقدان الإنتاجية
            ])->default('cost_compensation');
            
            $table->enum('cause', [
                'client_delay',          // تأخير العميل
                'design_changes',        // تغييرات التصميم
                'differing_conditions',  // ظروف مختلفة
                'force_majeure',         // قوة قاهرة
                'suspension',            // إيقاف
                'late_payment',          // تأخر الدفع
                'acceleration_order',    // أمر بالتسريع
                'other'                  // أخرى
            ])->default('client_delay');
            
            // القيم
            $table->decimal('claimed_amount', 18, 2)->default(0);
            $table->integer('claimed_days')->default(0);
            $table->decimal('assessed_amount', 18, 2)->default(0);
            $table->integer('assessed_days')->default(0);
            $table->decimal('approved_amount', 18, 2)->default(0);
            $table->integer('approved_days')->default(0);
            $table->string('currency', 3)->default('SAR');
            
            // التواريخ
            $table->date('event_start_date'); // بداية الحدث
            $table->date('event_end_date')->nullable();
            $table->date('notice_date'); // تاريخ الإشعار
            $table->date('submission_date')->nullable();
            $table->date('response_due_date')->nullable();
            $table->date('response_date')->nullable();
            $table->date('resolution_date')->nullable();
            
            // الحالة
            $table->enum('status', [
                'identified',     // تم تحديده
                'notice_sent',    // تم إرسال الإشعار
                'documenting',    // قيد التوثيق
                'submitted',      // مقدم
                'under_review',   // قيد المراجعة
                'negotiating',    // قيد التفاوض
                'approved',       // معتمد
                'partially_approved', // معتمد جزئياً
                'rejected',       // مرفوض
                'withdrawn',      // مسحوب
                'arbitration',    // تحكيم
                'litigation',     // تقاضي
                'settled'         // تمت التسوية
            ])->default('identified');
            
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // المراجعة
            $table->foreignId('prepared_by')->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->text('client_response')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->text('lessons_learned')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
