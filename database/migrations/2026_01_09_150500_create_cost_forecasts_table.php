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
        Schema::create('cost_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('forecast_date');
            $table->enum('forecast_type', ['monthly', 'quarterly', 'completion']);
            $table->integer('period_month')->nullable();
            $table->integer('period_year')->nullable();
            $table->foreignId('budget_item_id')->nullable()->constrained('project_budget_items')->nullOnDelete();
            $table->foreignId('cost_code_id')->constrained()->restrictOnDelete();
            $table->decimal('forecast_amount', 15, 2);
            $table->enum('basis', ['trend', 'percentage', 'manual'])->default('manual');
            $table->text('assumptions')->nullable();
            $table->foreignId('prepared_by_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'forecast_date']);
            $table->index(['project_id', 'period_year', 'period_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_forecasts');
    }
};
