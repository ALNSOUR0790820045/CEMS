<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_schedule_id')->constrained('project_schedules')->cascadeOnDelete();
            $table->string('update_number');
            $table->date('update_date');
            $table->date('data_date');
            $table->text('narrative')->nullable();
            $table->foreignId('updated_by_id')->constrained('users');
            $table->integer('activities_updated')->default(0);
            $table->integer('activities_added')->default(0);
            $table->integer('activities_deleted')->default(0);
            $table->integer('schedule_variance_days')->default(0);
            $table->boolean('critical_path_changed')->default(false);
            $table->timestamps();
            
            $table->index('project_schedule_id');
            $table->index('update_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_updates');
    }
};
