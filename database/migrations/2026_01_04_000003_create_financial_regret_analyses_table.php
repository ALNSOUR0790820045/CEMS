<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_regret_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('analysis_number')->unique();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('contract_id')->constrained();
            $table->date('analysis_date');
            
            // الوضع الحالي للمشروع
            $table->decimal('contract_value', 18, 2);
            $table->decimal('work_completed_value', 18, 2);
            $table->decimal('work_completed_percentage', 5, 2);
            $table->decimal('remaining_work_value', 18, 2);
            $table->integer('original_duration_days');
            $table->integer('elapsed_days');
            $table->integer('remaining_days');
            
            // تكاليف الاستمرار مع المقاول الحالي
            $table->decimal('continuation_remaining_cost', 18, 2);
            $table->decimal('continuation_claims_estimate', 18, 2)->default(0);
            $table->decimal('continuation_variations', 18, 2)->default(0);
            $table->decimal('continuation_total', 18, 2);
            
            // تكاليف إنهاء العقد
            $table->decimal('termination_payment_due', 18, 2); // مستحقات للمقاول
            $table->decimal('termination_demobilization', 18, 2)->default(0);
            $table->decimal('termination_claims', 18, 2)->default(0); // مطالبات متوقعة
            $table->decimal('termination_legal_costs', 18, 2)->default(0);
            $table->decimal('termination_total', 18, 2);
            
            // تكاليف مقاول جديد
            $table->decimal('new_contractor_mobilization', 18, 2);
            $table->decimal('new_contractor_learning_curve', 18, 2); // تكلفة منحنى التعلم
            $table->decimal('new_contractor_premium', 18, 2); // علاوة الدخول لمشروع قائم
            $table->decimal('new_contractor_remaining_work', 18, 2);
            $table->decimal('new_contractor_total', 18, 2);
            
            // تكلفة التأخير
            $table->integer('estimated_delay_days');
            $table->decimal('delay_cost_per_day', 15, 2);
            $table->decimal('total_delay_cost', 18, 2);
            
            // النتيجة
            $table->decimal('cost_to_terminate', 18, 2); // إجمالي تكلفة الإنهاء
            $table->decimal('cost_to_continue', 18, 2); // إجمالي تكلفة الاستمرار
            $table->decimal('regret_index', 18, 2); // مؤشر الندم
            $table->decimal('regret_percentage', 5, 2); // نسبة الندم
            
            $table->string('currency', 3)->default('JOD');
            
            $table->enum('recommendation', ['continue', 'negotiate', 'review'])->default('continue');
            $table->text('analysis_notes')->nullable();
            $table->text('negotiation_points')->nullable();
            
            $table->foreignId('prepared_by')->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_regret_analyses');
    }
};
