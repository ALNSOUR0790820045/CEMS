<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_schedule_id')->constrained('project_schedules')->cascadeOnDelete();
            $table->string('activity_code');
            $table->foreignId('wbs_id')->nullable()->constrained('project_wbs')->nullOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->enum('activity_type', ['task', 'milestone', 'summary', 'hammock'])->default('task');
            $table->foreignId('parent_id')->nullable()->constrained('schedule_activities')->nullOnDelete();
            $table->integer('level')->default(1);
            
            // Planned dates
            $table->date('planned_start')->nullable();
            $table->date('planned_finish')->nullable();
            $table->integer('planned_duration')->default(0);
            
            // Actual dates
            $table->date('actual_start')->nullable();
            $table->date('actual_finish')->nullable();
            $table->integer('actual_duration')->default(0);
            $table->integer('remaining_duration')->default(0);
            $table->decimal('percent_complete', 5, 2)->default(0);
            
            // CPM calculated fields
            $table->date('early_start')->nullable();
            $table->date('early_finish')->nullable();
            $table->date('late_start')->nullable();
            $table->date('late_finish')->nullable();
            $table->integer('total_float')->default(0);
            $table->integer('free_float')->default(0);
            $table->boolean('is_critical')->default(false);
            
            // Constraints
            $table->enum('constraint_type', ['asap', 'alap', 'snet', 'snlt', 'fnet', 'fnlt', 'mso', 'mfo'])->default('asap');
            $table->date('constraint_date')->nullable();
            
            $table->foreignId('calendar_id')->nullable()->constrained('schedule_calendars');
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->foreignId('cost_account_id')->nullable()->constrained('accounts');
            
            // Cost fields
            $table->decimal('budgeted_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->decimal('earned_value', 15, 2)->default(0);
            
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started');
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('project_schedule_id');
            $table->index('activity_code');
            $table->index('status');
            $table->index('is_critical');
            $table->unique(['project_schedule_id', 'activity_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_activities');
    }
};
