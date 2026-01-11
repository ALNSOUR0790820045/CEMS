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
        Schema::create('diary_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained('site_diaries')->onDelete('cascade');
            $table->string('equipment_type');
            $table->integer('quantity')->default(1);
            $table->decimal('hours_worked', 5, 2)->default(0.00);
            $table->decimal('hours_idle', 5, 2)->default(0.00);
            $table->text('idle_reason')->nullable();
            $table->decimal('fuel_consumed', 8, 2)->nullable();
            $table->string('operator_name')->nullable();
            $table->enum('status', ['working', 'idle', 'breakdown', 'maintenance'])->default('working');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_equipment');
    }
};
