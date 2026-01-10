<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // سجل المخاطر المحققة
        Schema::create('tender_risk_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_risk_id')->constrained()->cascadeOnDelete();
            $table->date('occurred_date');
            $table->text('description');
            $table->decimal('actual_cost_impact', 15, 2)->default(0);
            $table->integer('actual_schedule_impact_days')->default(0);
            $table->text('actions_taken')->nullable();
            $table->foreignId('reported_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_risk_events');
    }
};
