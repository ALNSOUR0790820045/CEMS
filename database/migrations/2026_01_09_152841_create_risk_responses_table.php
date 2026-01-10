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
        Schema::create('risk_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained()->cascadeOnDelete();
            $table->string('response_number');
            $table->enum('response_type', ['preventive', 'corrective', 'contingency']);
            $table->enum('strategy', ['avoid', 'mitigate', 'transfer', 'accept']);
            $table->text('description');
            $table->text('action_required')->nullable();
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->date('target_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->decimal('cost_of_response', 15, 2)->nullable();
            $table->enum('effectiveness', ['not_started', 'in_progress', 'effective', 'partially_effective', 'ineffective'])->default('not_started');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_responses');
    }
};
