<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_schedule_id')->constrained('project_schedules')->cascadeOnDelete();
            $table->foreignId('predecessor_id')->constrained('schedule_activities')->cascadeOnDelete();
            $table->foreignId('successor_id')->constrained('schedule_activities')->cascadeOnDelete();
            $table->enum('dependency_type', ['FS', 'FF', 'SS', 'SF'])->default('FS');
            $table->integer('lag_days')->default(0);
            $table->enum('lag_type', ['days', 'percentage'])->default('days');
            $table->boolean('is_driving')->default(false);
            $table->timestamps();
            
            $table->unique(['predecessor_id', 'successor_id']);
            $table->index('project_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_dependencies');
    }
};
