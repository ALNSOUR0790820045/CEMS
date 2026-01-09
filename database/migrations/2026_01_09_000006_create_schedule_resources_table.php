<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_activity_id')->constrained('schedule_activities')->cascadeOnDelete();
            $table->enum('resource_type', ['labor', 'equipment', 'material']);
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('resource_name');
            $table->decimal('planned_units', 10, 2)->default(0);
            $table->decimal('actual_units', 10, 2)->default(0);
            $table->decimal('remaining_units', 10, 2)->default(0);
            $table->enum('unit_type', ['hours', 'days', 'quantity'])->default('hours');
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('planned_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->timestamps();
            
            $table->index('schedule_activity_id');
            $table->index(['resource_type', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_resources');
    }
};
