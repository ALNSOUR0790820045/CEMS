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
        Schema::create('cash_forecasts', function (Blueprint $table) {
            $table->id();
            $table->date('forecast_date');
            $table->enum('forecast_type', ['inflow', 'outflow']);
            $table->enum('category', ['receivables', 'payables', 'payroll', 'expenses', 'loans', 'other']);
            $table->decimal('expected_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2)->nullable();
            $table->decimal('variance', 15, 2)->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('probability_percentage')->default(100);
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_forecasts');
    }
};
