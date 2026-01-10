<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_bar_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique(); // TBE-2026-0001
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            
            // تفاصيل الحدث
            $table->string('title');
            $table->text('description');
            $table->date('event_date'); // تاريخ وقوع الحدث
            $table->date('discovery_date'); // تاريخ اكتشاف الحدث
            
            // نوع الحدث
            $table->enum('event_type', [
                'delay',                    // تأخير
                'disruption',               // إعاقة
                'variation_instruction',    // تعليمات تغيير
                'differing_conditions',     // ظروف مختلفة
                'force_majeure',            // قوة قاهرة
                'suspension',               // إيقاف
                'client_default',           // إخلال العميل
                'design_error',             // خطأ تصميم
                'late_information',         // تأخر المعلومات
                'access_delay',             // تأخر الوصول للموقع
                'payment_delay',            // تأخر الدفع
                'other'
            ]);
            
            // المواعيد
            $table->integer('notice_period_days')->default(28); // فترة الإشعار (من العقد)
            $table->date('notice_deadline'); // آخر موعد للإشعار
            $table->integer('days_remaining')->default(28);
            
            // الإشعار
            $table->boolean('notice_sent')->default(false);
            $table->date('notice_sent_date')->nullable();
            $table->string('notice_reference')->nullable();
            $table->foreignId('notice_correspondence_id')->nullable()->constrained('correspondence')->nullOnDelete();
            
            // التأثير المتوقع
            $table->integer('estimated_delay_days')->default(0);
            $table->decimal('estimated_cost_impact', 18, 2)->default(0);
            $table->string('currency', 3)->default('JOD');
            
            // الحالة
            $table->enum('status', [
                'identified',       // تم تحديده
                'notice_pending',   // بانتظار الإشعار
                'notice_sent',      // تم الإشعار
                'claim_submitted',  // تم تقديم المطالبة
                'resolved',         // تمت التسوية
                'time_barred',      // سقط الحق (فات الموعد!)
                'cancelled'         // ملغي
            ])->default('identified');
            
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('high');
            
            // الربط بالمطالبة
            $table->foreignId('claim_id')->nullable()->constrained('claims')->nullOnDelete();
            $table->foreignId('variation_order_id')->nullable()->constrained('variation_orders')->nullOnDelete();
            
            // المسؤولية
            $table->foreignId('identified_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('event_date');
            $table->index('notice_deadline');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_bar_events');
    }
};
