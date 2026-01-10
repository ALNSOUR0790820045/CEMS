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
        Schema::create('risk_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number')->unique(); // RI-YYYY-XXXX
            $table->foreignId('risk_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('incident_date');
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->decimal('actual_cost_impact', 15, 2)->nullable();
            $table->integer('actual_schedule_impact')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('immediate_actions')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->text('preventive_actions')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->foreignId('reported_by_id')->constrained('users');
            $table->enum('status', ['reported', 'investigating', 'resolved', 'closed'])->default('reported');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_incidents');
    }
};
