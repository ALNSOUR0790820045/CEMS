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
        Schema::create('project_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('report_date');
            $table->integer('report_number');
            $table->enum('period_type', ['daily', 'weekly', 'monthly'])->default('weekly');
            $table->decimal('physical_progress', 5, 2);
            $table->decimal('planned_progress', 5, 2);
            $table->decimal('variance', 5, 2);
            $table->text('work_done')->nullable();
            $table->text('planned_work')->nullable();
            $table->text('issues')->nullable();
            $table->text('recommendations')->nullable();
            $table->integer('manpower_count')->default(0);
            $table->integer('equipment_count')->default(0);
            $table->enum('weather', ['sunny', 'cloudy', 'rainy', 'sandstorm'])->default('sunny');
            $table->foreignId('prepared_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_progress_reports');
    }
};
