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
        Schema::create('cost_plus_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained();
            
            // نوع الربح
            $table->enum('fee_type', [
                'percentage',       // نسبة مئوية
                'fixed_fee',        // مبلغ مقطوع
                'incentive',        // حوافز أداء
                'hybrid'            // هجين
            ])->default('percentage');
            
            $table->decimal('fee_percentage', 5, 2)->nullable();
            $table->decimal('fixed_fee_amount', 18, 2)->nullable();
            
            // السقف الأقصى (GMP)
            $table->boolean('has_gmp')->default(false);
            $table->decimal('guaranteed_maximum_price', 18, 2)->nullable();
            $table->decimal('gmp_savings_share', 5, 2)->default(50); // حصة المقاول من الوفورات
            
            // المصاريف غير المباشرة
            $table->boolean('overhead_reimbursable')->default(true);
            $table->decimal('overhead_percentage', 5, 2)->nullable();
            $table->enum('overhead_method', ['percentage', 'actual', 'allocated'])->default('percentage');
            
            // قواعد التكاليف
            $table->json('reimbursable_costs')->default(json_encode([])); // التكاليف القابلة للاسترداد
            $table->json('non_reimbursable_costs')->default(json_encode([])); // التكاليف غير القابلة للاسترداد
            
            $table->string('currency', 3)->default('JOD');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_plus_contracts');
    }
};
