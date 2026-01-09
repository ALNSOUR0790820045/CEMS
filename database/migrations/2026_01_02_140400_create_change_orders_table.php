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
        Schema::create('change_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('co_number')->unique(); // CO-2026-001
            $table->date('issue_date');
            
            // الربط بالعطاء الأصلي
            $table->foreignId('tender_id')->nullable()->constrained();
            $table->foreignId('original_contract_id')->nullable()->constrained('contracts');
            
            // التصنيف
            $table->enum('type', ['scope_change', 'quantity_change', 'design_change', 'specification_change', 'other'])->default('scope_change');
            $table->enum('reason', ['client_request', 'design_error', 'site_condition', 'regulatory', 'other'])->default('client_request');
            
            // الوصف
            $table->string('title');
            $table->text('description');
            $table->text('justification')->nullable();
            
            // التحليل المالي
            $table->decimal('original_contract_value', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0); // قيمة التغيير (موجب أو سالب)
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            // الرسوم التلقائية (حسب النسبة من قيمة العقد الأصلي)
            $table->decimal('fee_percentage', 5, 3)->default(0.003); // 0.3% default
            $table->decimal('calculated_fee', 15, 2)->default(0);
            $table->decimal('stamp_duty', 15, 2)->default(0);
            $table->decimal('total_fees', 15, 2)->default(0);
            
            // الأثر على الجدول الزمني
            $table->integer('time_extension_days')->default(0);
            $table->date('new_completion_date')->nullable();
            $table->text('schedule_impact_description')->nullable();
            
            // الحالة
            $table->enum('status', ['draft', 'pending_pm', 'pending_technical', 'pending_consultant', 'pending_client', 'approved', 'rejected', 'cancelled'])->default('draft');
            
            // سلسلة التوقيعات (4 مستويات)
            // 1. مدير المشروع
            $table->foreignId('pm_user_id')->nullable()->constrained('users');
            $table->timestamp('pm_signed_at')->nullable();
            $table->enum('pm_decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('pm_comments')->nullable();
            
            // 2. المدير الفني
            $table->foreignId('technical_user_id')->nullable()->constrained('users');
            $table->timestamp('technical_signed_at')->nullable();
            $table->enum('technical_decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('technical_comments')->nullable();
            
            // 3. الاستشاري
            $table->foreignId('consultant_user_id')->nullable()->constrained('users');
            $table->timestamp('consultant_signed_at')->nullable();
            $table->enum('consultant_decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('consultant_comments')->nullable();
            
            // 4. العميل
            $table->foreignId('client_user_id')->nullable()->constrained('users');
            $table->timestamp('client_signed_at')->nullable();
            $table->enum('client_decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('client_comments')->nullable();
            
            // المرفقات والمستندات
            $table->json('attachments')->nullable();
            
            // القيمة المحدثة للعقد
            $table->decimal('updated_contract_value', 15, 2)->default(0);
            
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_orders');
    }
};
