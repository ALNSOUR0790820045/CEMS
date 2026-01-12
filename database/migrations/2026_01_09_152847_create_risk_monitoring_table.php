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
        Schema::create('risk_monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained()->cascadeOnDelete();
            $table->date('monitoring_date');
            $table->foreignId('monitored_by_id')->constrained('users');
            $table->string('current_status');
            $table->enum('probability_change', ['increased', 'same', 'decreased'])->default('same');
            $table->enum('impact_change', ['increased', 'same', 'decreased'])->default('same');
            $table->enum('trigger_status', ['not_triggered', 'warning', 'triggered'])->default('not_triggered');
            $table->text('early_warning_signs')->nullable();
            $table->text('actions_taken')->nullable();
            $table->text('effectiveness')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('next_review_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_monitoring');
    }
};
