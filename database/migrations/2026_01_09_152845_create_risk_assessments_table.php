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
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained()->cascadeOnDelete();
            $table->date('assessment_date');
            $table->enum('assessment_type', ['initial', 'reassessment', 'post_response']);
            $table->foreignId('assessed_by_id')->constrained('users');
            $table->enum('probability', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->integer('probability_score'); // 1-5
            $table->enum('impact', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->integer('impact_score'); // 1-5
            $table->integer('risk_score'); // probability_score × impact_score
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical']);
            $table->decimal('cost_impact', 15, 2)->nullable();
            $table->integer('schedule_impact')->nullable();
            $table->text('justification')->nullable();
            $table->text('recommendations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_assessments');
    }
};
