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
        Schema::create('kpi_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->enum('category', ['financial', 'operational', 'hr', 'project'])->default('operational');
            $table->text('calculation_formula')->nullable();
            $table->enum('unit', ['percentage', 'currency', 'number', 'days'])->default('number');
            $table->decimal('target_value', 18, 2)->nullable();
            $table->decimal('warning_threshold', 18, 2)->nullable();
            $table->decimal('critical_threshold', 18, 2)->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly'])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_definitions');
    }
};
