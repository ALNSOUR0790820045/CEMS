<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number')->unique(); // TND-2026-001
            $table->string('reference_number')->nullable(); // رقم العطاء الحكومي
            
            // معلومات العطاء
            $table->string('tender_name');
            $table->string('tender_name_en')->nullable();
            $table->text('description');
            $table->text('description_en')->nullable();
            
            // الجهة المالكة
            $table->string('owner_name'); // الوزارة/المؤسسة
            $table->string('owner_contact')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('owner_phone')->nullable();
            
            // الموقع
            $table->foreignId('country_id')->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->text('project_location')->nullable();
            
            // التصنيف
            $table->enum('tender_type', [
                'construction',      // إنشاءات
                'infrastructure',    // بنية تحتية
                'buildings',         // مباني
                'roads',            // طرق
                'bridges',          // جسور
                'water',            // مياه وصرف صحي
                'electrical',       // كهرباء
                'mechanical',       // ميكانيكا
                'maintenance',      // صيانة
                'consultancy',      // استشارات
                'other'
            ]);
            
            $table->enum('contract_type', [
                'lump_sum',         // مقطوعية
                'unit_price',       // أسعار وحدات
                'cost_plus',        // تكلفة + ربح
                'time_material',    // مياومة
                'design_build',     // تصميم وتنفيذ
                'epc',             // EPC
                'bot',             // BOT
                'other'
            ])->default('lump_sum');
            
            // القيمة التقديرية
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->foreignId('currency_id')->constrained();
            
            // المدة
            $table->integer('estimated_duration_months')->nullable();
            
            // التواريخ المهمة
            $table->date('announcement_date')->nullable();
            $table->date('document_sale_start')->nullable();
            $table->date('document_sale_end')->nullable();
            $table->decimal('document_price', 10, 2)->default(0);
            
            $table->date('site_visit_date')->nullable();
            $table->time('site_visit_time')->nullable();
            
            $table->date('questions_deadline')->nullable();
            $table->date('submission_deadline');
            $table->time('submission_time')->nullable();
            
            $table->date('opening_date')->nullable();
            $table->time('opening_time')->nullable();
            
            // كفالة العطاء (Bid Bond)
            $table->boolean('requires_bid_bond')->default(true);
            $table->decimal('bid_bond_percentage', 5, 2)->default(1.00); // 1%
            $table->decimal('bid_bond_amount', 15, 2)->nullable();
            $table->integer('bid_bond_validity_days')->default(90);
            
            // متطلبات التأهيل
            $table->json('prequalification_requirements')->nullable();
            $table->text('eligibility_criteria')->nullable();
            
            // الحالة
            $table->enum('status', [
                'announced',        // معلن
                'evaluating',       // قيد التقييم
                'decision_pending', // قيد اتخاذ القرار
                'preparing',        // قيد التحضير
                'submitted',        // تم التقديم
                'awarded',          // تمت الترسية علينا
                'lost',            // خسرنا
                'cancelled',       // ألغي
                'passed'           // لم نتقدم
            ])->default('announced');
            
            // قرار المشاركة
            $table->boolean('participate')->nullable();
            $table->text('participation_decision_notes')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users');
            $table->date('decision_date')->nullable();
            
            // المستندات
            $table->json('tender_documents')->nullable(); // وثائق العطاء
            $table->json('our_documents')->nullable(); // مستنداتنا
            
            // الملاحظات
            $table->text('notes')->nullable();
            
            // المسؤول
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
