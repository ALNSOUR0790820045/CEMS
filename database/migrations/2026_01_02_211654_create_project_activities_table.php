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
        Schema::create('project_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wbs_id')->constrained('project_wbs'); // ربط بـ WBS
            $table->string('activity_code')->unique(); // ACT-001
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            
            // التواريخ المخططة
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->integer('planned_duration_days');
            
            // التواريخ الفعلية
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->integer('actual_duration_days')->nullable();
            
            // المدة والجهد
            $table->decimal('planned_effort_hours', 10, 2)->default(0);
            $table->decimal('actual_effort_hours', 10, 2)->default(0);
            
            // نسبة الإنجاز
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->enum('progress_method', ['manual', 'duration', 'effort', 'units'])->default('manual');
            
            // التصنيف
            $table->enum('type', ['task', 'milestone', 'summary'])->default('task');
            $table->boolean('is_critical')->default(false); // Critical Path
            $table->integer('total_float_days')->default(0); // مرونة
            $table->integer('free_float_days')->default(0);
            
            // المسؤولية
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('not_started');
            
            // التكلفة
            $table->decimal('budgeted_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            
            // الأولوية
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_activities');
    }
};
