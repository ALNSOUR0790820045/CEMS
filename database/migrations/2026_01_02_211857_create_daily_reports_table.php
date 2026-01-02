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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('report_number')->unique(); // DR-2026-001
            $table->date('report_date');
            
            // الطقس والظروف
            $table->string('weather_condition')->nullable(); // صافي، غائم، ممطر
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->text('site_conditions')->nullable();
            
            // ساعات العمل
            $table->time('work_start_time')->nullable();
            $table->time('work_end_time')->nullable();
            $table->decimal('total_work_hours', 5, 2)->default(8);
            
            // العمالة
            $table->integer('workers_count')->default(0);
            $table->json('workers_breakdown')->nullable(); // مهندسين، فنيين، عمال...
            $table->text('attendance_notes')->nullable();
            
            // المعدات (ساعات التشغيل)
            $table->json('equipment_hours')->nullable(); // [{equipment_id, hours}, ...]
            $table->text('equipment_notes')->nullable();
            
            // الأعمال المنفذة
            $table->text('work_executed')->nullable();
            $table->json('activities_progress')->nullable(); // [{activity_id, progress_today}, ...]
            $table->text('quality_notes')->nullable();
            
            // المواد المستلمة
            $table->json('materials_received')->nullable();
            $table->text('materials_notes')->nullable();
            
            // المشاكل والتأخيرات
            $table->text('problems')->nullable();
            $table->text('delays')->nullable();
            $table->text('safety_incidents')->nullable();
            
            // الزوار والاجتماعات
            $table->json('visitors')->nullable(); // [{name, company, purpose, time}, ...]
            $table->text('meetings')->nullable();
            
            // التعليمات والملاحظات
            $table->text('instructions_received')->nullable();
            $table->text('general_notes')->nullable();
            
            // الحالة والموافقات
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            
            // التوقيعات (مع الوقت)
            $table->foreignId('prepared_by')->constrained('users'); // مهندس الموقع
            $table->timestamp('prepared_at')->nullable();
            
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // مدير المشروع
            $table->timestamp('reviewed_at')->nullable();
            
            $table->foreignId('consultant_approved_by')->nullable()->constrained('users'); // الاستشاري
            $table->timestamp('consultant_approved_at')->nullable();
            
            $table->foreignId('client_approved_by')->nullable()->constrained('users'); // العميل
            $table->timestamp('client_approved_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['project_id', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
