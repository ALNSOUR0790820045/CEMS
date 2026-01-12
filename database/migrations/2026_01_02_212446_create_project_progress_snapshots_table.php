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
        Schema::create('project_progress_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('snapshot_date'); // تاريخ التقرير
            
            // نسب الإنجاز
            $table->decimal('overall_progress_percent', 5, 2)->default(0);
            $table->decimal('planned_progress_percent', 5, 2)->default(0);
            $table->decimal('physical_progress_percent', 5, 2)->default(0);
            
            // Earned Value Analysis
            $table->decimal('planned_value_pv', 15, 2)->default(0); // الكلفة المخططة للعمل المجدول
            $table->decimal('earned_value_ev', 15, 2)->default(0); // الكلفة المخططة للعمل المنجز
            $table->decimal('actual_cost_ac', 15, 2)->default(0); // الكلفة الفعلية للعمل المنجز
            
            // Budget
            $table->decimal('budget_at_completion_bac', 15, 2)->default(0);
            $table->decimal('estimate_at_completion_eac', 15, 2)->default(0);
            $table->decimal('estimate_to_complete_etc', 15, 2)->default(0);
            $table->decimal('variance_at_completion_vac', 15, 2)->default(0);
            
            // Variances
            $table->decimal('schedule_variance_sv', 15, 2)->default(0); // EV - PV
            $table->decimal('cost_variance_cv', 15, 2)->default(0); // EV - AC
            
            // Performance Indexes
            $table->decimal('schedule_performance_index_spi', 5, 3)->default(1.000); // EV / PV
            $table->decimal('cost_performance_index_cpi', 5, 3)->default(1.000); // EV / AC
            $table->decimal('to_complete_performance_index_tcpi', 5, 3)->default(1.000);
            
            // تواريخ
            $table->date('planned_completion_date')->nullable();
            $table->date('forecasted_completion_date')->nullable();
            
            $table->text('comments')->nullable();
            $table->foreignId('reported_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['project_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_progress_snapshots');
    }
};
