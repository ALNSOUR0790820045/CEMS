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
        Schema::create('equipment_fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('fill_date');
            $table->decimal('quantity', 10, 2); // لترات
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_cost', 15, 2);
            $table->decimal('meter_reading', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_fuel_logs');
    }
};
