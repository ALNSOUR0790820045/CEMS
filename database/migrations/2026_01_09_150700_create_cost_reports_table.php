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
        Schema::create('cost_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('report_date');
            $table->enum('report_type', ['weekly', 'monthly', 'quarterly']);
            $table->date('period_from');
            $table->date('period_to');
            $table->decimal('original_budget', 15, 2)->default(0);
            $table->decimal('revised_budget', 15, 2)->default(0);
            $table->decimal('committed_costs', 15, 2)->default(0);
            $table->decimal('actual_costs', 15, 2)->default(0);
            $table->decimal('forecast_at_completion', 15, 2)->default(0);
            $table->decimal('variance_to_budget', 15, 2)->default(0);
            $table->decimal('percentage_complete', 5, 2)->default(0);
            $table->decimal('cost_performance_index', 10, 4)->default(0);
            $table->decimal('schedule_performance_index', 10, 4)->default(0);
            $table->decimal('earned_value', 15, 2)->default(0);
            $table->foreignId('prepared_by_id')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'report_date']);
            $table->index(['report_type', 'period_from', 'period_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_reports');
    }
};
