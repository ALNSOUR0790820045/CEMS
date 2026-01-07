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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_number')->unique(); // PRJ-2026-0001
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            
            // المصدر
            $table->foreignId('tender_id')->nullable()->constrained();
            $table->foreignId('contract_id')->nullable()->constrained();
            
            // العميل
            $table->foreignId('client_id')->constrained();
            $table->string('client_contract_number')->nullable();
            
            // التصنيف
            $table->enum('type', ['building', 'infrastructure', 'industrial', 'maintenance', 'fit_out', 'other'])->default('building');
            $table->enum('category', ['new_construction', 'renovation', 'expansion', 'maintenance'])->default('new_construction');
            
            // الموقع
            $table->string('location');
            $table->string('city');
            $table->string('region')->nullable();
            $table->string('country')->default('Saudi Arabia');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // التواريخ
            $table->date('award_date')->nullable();
            $table->date('contract_date')->nullable();
            $table->date('commencement_date');
            $table->date('original_completion_date');
            $table->date('revised_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->date('handover_date')->nullable();
            $table->date('final_handover_date')->nullable();
            $table->integer('original_duration_days');
            $table->integer('approved_extension_days')->default(0);
            
            // القيم المالية
            $table->decimal('original_contract_value', 18, 2);
            $table->decimal('approved_variations', 18, 2)->default(0);
            $table->decimal('revised_contract_value', 18, 2);
            $table->string('currency', 3)->default('SAR');
            $table->decimal('advance_payment_percentage', 5, 2)->default(0);
            $table->decimal('advance_payment_amount', 18, 2)->default(0);
            $table->decimal('retention_percentage', 5, 2)->default(10);
            $table->decimal('performance_bond_percentage', 5, 2)->default(10);
            
            // التقدم
            $table->decimal('physical_progress', 5, 2)->default(0); // نسبة الإنجاز الفعلي
            $table->decimal('financial_progress', 5, 2)->default(0); // نسبة الصرف
            $table->decimal('time_progress', 5, 2)->default(0); // نسبة الوقت المنقضي
            
            // الحالة
            $table->enum('status', [
                'not_started',     // لم يبدأ
                'mobilization',    // تجهيز الموقع
                'in_progress',     // قيد التنفيذ
                'on_hold',         // متوقف
                'suspended',       // معلق
                'completed',       // منتهي
                'handed_over',     // تم التسليم الابتدائي
                'final_handover',  // تم التسليم النهائي
                'closed',          // مغلق
                'terminated'       // ملغي
            ])->default('not_started');
            
            $table->enum('health', ['on_track', 'at_risk', 'delayed', 'critical'])->default('on_track');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // الفريق
            $table->foreignId('project_manager_id')->nullable()->constrained('users');
            $table->foreignId('site_engineer_id')->nullable()->constrained('users');
            $table->foreignId('quantity_surveyor_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            
            // الضمانات
            $table->foreignId('performance_bond_id')->nullable()->constrained('guarantees');
            $table->foreignId('advance_bond_id')->nullable()->constrained('guarantees');
            
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
        Schema::dropIfExists('projects');
    }
};
