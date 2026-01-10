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
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->nullable()->constrained('project_phases');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('target_date');
            $table->date('actual_date')->nullable();
            $table->enum('type', ['contractual', 'internal', 'payment'])->default('internal');
            $table->boolean('is_critical')->default(false);
            $table->enum('status', ['pending', 'achieved', 'delayed', 'missed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
