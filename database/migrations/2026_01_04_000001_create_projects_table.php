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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'completed', 'on_hold', 'delayed'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('planned_value', 15, 2)->default(0);
            $table->decimal('earned_value', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->decimal('budget', 15, 2)->default(0);
            $table->integer('progress')->default(0);
            $table->string('client_name')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
