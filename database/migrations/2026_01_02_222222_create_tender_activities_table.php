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
        Schema::create('tender_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_wbs_id')->nullable()->constrained('tender_wbs');
            $table->string('activity_code')->unique(); // TACT-001
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            
            // المدة
            $table->integer('duration_days');
            $table->decimal('effort_hours', 10, 2)->default(0);
            
            // التواريخ المخططة (بعد تحديد تاريخ البداية)
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            
            // CPM Analysis
            $table->integer('early_start')->nullable();
            $table->integer('early_finish')->nullable();
            $table->integer('late_start')->nullable();
            $table->integer('late_finish')->nullable();
            $table->integer('total_float')->default(0);
            $table->integer('free_float')->default(0);
            $table->boolean('is_critical')->default(false);
            
            // التصنيف
            $table->enum('type', ['task', 'milestone', 'summary'])->default('task');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // التكلفة
            $table->decimal('estimated_cost', 15, 2)->default(0);
            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_activities');
    }
};
