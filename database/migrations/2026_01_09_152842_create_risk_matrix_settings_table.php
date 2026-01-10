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
        Schema::create('risk_matrix_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->json('probability_levels'); // [{level, score, description}]
            $table->json('impact_levels'); // [{level, score, description}]
            $table->json('risk_thresholds'); // {low: '0-4', medium: '5-9', high: '10-15', critical: '16-25'}
            $table->json('cost_impact_ranges')->nullable();
            $table->json('schedule_impact_ranges')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_matrix_settings');
    }
};
