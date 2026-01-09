<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_baselines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_schedule_id')->constrained('project_schedules')->cascadeOnDelete();
            $table->string('baseline_number');
            $table->string('baseline_name');
            $table->date('baseline_date');
            $table->foreignId('created_by_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('project_schedule_id');
            $table->index('baseline_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_baselines');
    }
};
