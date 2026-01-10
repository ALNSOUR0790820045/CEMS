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
        Schema::create('kpi_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_definition_id')->constrained('kpi_definitions')->cascadeOnDelete();
            $table->date('period_date');
            $table->decimal('actual_value', 18, 2)->nullable();
            $table->decimal('target_value', 18, 2)->nullable();
            $table->decimal('variance', 18, 2)->nullable();
            $table->decimal('variance_percentage', 8, 2)->nullable();
            $table->enum('status', ['on_track', 'warning', 'critical'])->default('on_track');
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_values');
    }
};
