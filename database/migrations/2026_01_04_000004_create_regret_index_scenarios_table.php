<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regret_index_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained('financial_regret_analyses')->cascadeOnDelete();
            $table->string('scenario_name');
            $table->enum('scenario_type', ['optimistic', 'realistic', 'pessimistic']);
            $table->json('assumptions');
            $table->decimal('regret_index', 18, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regret_index_scenarios');
    }
};
