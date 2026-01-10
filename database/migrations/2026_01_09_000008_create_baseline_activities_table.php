<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baseline_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_baseline_id')->constrained('schedule_baselines')->cascadeOnDelete();
            $table->foreignId('schedule_activity_id')->constrained('schedule_activities')->cascadeOnDelete();
            $table->date('planned_start');
            $table->date('planned_finish');
            $table->integer('planned_duration');
            $table->decimal('budgeted_cost', 15, 2)->default(0);
            $table->timestamps();
            
            $table->index('schedule_baseline_id');
            $table->index('schedule_activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baseline_activities');
    }
};
