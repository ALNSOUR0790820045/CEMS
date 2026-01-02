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
        Schema::create('price_escalation_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_escalation_contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('main_ipc_id')->nullable()->constrained(); // ربط بالمستخلص
            
            $table->string('calculation_number')->unique(); // PE-2026-001
            $table->date('calculation_date');
            $table->date('period_from');
            $table->date('period_to');
            
            // القيم المرجعية (Base)
            $table->decimal('base_materials_index', 10, 4); // L₀
            $table->decimal('base_labor_index', 10, 4);     // P₀
            
            // القيم الحالية (Current)
            $table->decimal('current_materials_index', 10, 4); // L₁
            $table->decimal('current_labor_index', 10, 4);     // P₁
            
            // التغير
            $table->decimal('materials_change_percent', 5, 2); // (L₁-L₀)/L₀ × 100
            $table->decimal('labor_change_percent', 5, 2);     // (P₁-P₀)/P₀ × 100
            
            // المعادلة:
            // E = (A × ΔL) + (B × ΔP)
            // حيث:
            // E = نسبة فروقات الأسعار
            // A = نسبة المواد (70%)
            // B = نسبة العمالة (30%)
            // ΔL = نسبة التغير في مؤشر المواد
            // ΔP = نسبة التغير في مؤشر العمالة
            
            $table->decimal('escalation_percentage', 5, 2); // E
            
            // التطبيق على المبلغ
            $table->decimal('ipc_amount', 15, 2); // مبلغ المستخلص
            $table->decimal('escalation_amount', 15, 2); // القيمة المضافة
            
            // عتبة التطبيق (Threshold)
            $table->boolean('threshold_met')->default(false); // هل تجاوز الـ 5%؟
            $table->boolean('applied')->default(false);
            
            // الحالة
            $table->enum('status', [
                'calculated',
                'pending_approval',
                'approved',
                'paid',
                'rejected'
            ])->default('calculated');
            
            // الموافقات
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_escalation_calculations');
    }
};
