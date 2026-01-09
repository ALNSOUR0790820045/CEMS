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
        Schema::create('project_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('budget_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('budget_type', ['original', 'revised', 'forecast'])->default('original');
            $table->integer('version')->default(1);
            $table->date('budget_date');
            $table->decimal('contract_value', 15, 2)->default(0);
            $table->decimal('direct_costs', 15, 2)->default(0);
            $table->decimal('indirect_costs', 15, 2)->default(0);
            $table->decimal('contingency_percentage', 5, 2)->default(0);
            $table->decimal('contingency_amount', 15, 2)->default(0);
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->decimal('profit_margin_percentage', 5, 2)->default(0);
            $table->decimal('expected_profit', 15, 2)->default(0);
            $table->foreignId('currency_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['draft', 'approved', 'active', 'closed'])->default('draft');
            $table->foreignId('prepared_by_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['company_id', 'budget_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_budgets');
    }
};
