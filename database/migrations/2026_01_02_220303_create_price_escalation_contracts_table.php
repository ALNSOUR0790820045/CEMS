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
        Schema::create('price_escalation_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->unique();
            
            // معلومات العقد
            $table->date('contract_date'); // تاريخ العقد (التاريخ المرجعي)
            $table->decimal('contract_amount', 15, 2);
            
            // معادلة فروقات الأسعار
            $table->enum('formula_type', [
                'dsi',              // دائرة الإحصاءات العامة (الأردن)
                'fixed_percentage', // نسبة ثابتة
                'custom_indices',   // مؤشرات مخصصة
                'none'
            ])->default('dsi');
            
            // نسب المعادلة (مثال: 70% مواد + 30% عمالة)
            $table->decimal('materials_weight', 5, 2)->default(70.00); // A
            $table->decimal('labor_weight', 5, 2)->default(30.00);     // B
            $table->decimal('fixed_portion', 5, 2)->default(0.00);     // C (ثابت)
            
            // التحقق: A + B + C = 100%
            
            // القيم المرجعية (Base Indices)
            $table->decimal('base_materials_index', 10, 4)->nullable(); // L₀
            $table->decimal('base_labor_index', 10, 4)->nullable();     // P₀
            
            // الحدود
            $table->decimal('threshold_percentage', 5, 2)->default(5.00); // لا تطبق إلا إذا > 5%
            $table->decimal('max_escalation_percentage', 5, 2)->nullable(); // سقف (اختياري)
            
            // التكرار
            $table->enum('calculation_frequency', [
                'monthly',
                'quarterly',
                'per_ipc',
                'annual'
            ])->default('per_ipc');
            
            // الحالة
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_escalation_contracts');
    }
};
