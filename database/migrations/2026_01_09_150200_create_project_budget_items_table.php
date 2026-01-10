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
        Schema::create('project_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_budget_id')->constrained('project_budgets')->cascadeOnDelete();
            $table->foreignId('cost_code_id')->constrained()->restrictOnDelete();
            $table->foreignId('boq_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('wbs_id')->nullable()->constrained('project_wbs')->nullOnDelete();
            $table->text('description');
            $table->enum('cost_type', ['material', 'labor', 'equipment', 'subcontractor', 'overhead', 'other'])->default('other');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('unit_rate', 15, 2)->default(0);
            $table->decimal('budgeted_amount', 15, 2)->default(0);
            $table->decimal('committed_amount', 15, 2)->default(0);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->decimal('variance_amount', 15, 2)->default(0);
            $table->decimal('variance_percentage', 5, 2)->default(0);
            $table->decimal('forecast_amount', 15, 2)->default(0);
            $table->decimal('estimate_to_complete', 15, 2)->default(0);
            $table->decimal('estimate_at_completion', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_budget_id', 'cost_code_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_budget_items');
    }
};
