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
        Schema::create('equipment_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->string('maintenance_number'); // MNT-EQP001-001
            $table->enum('type', [
                'preventive',    // وقائية
                'corrective',    // تصحيحية
                'breakdown',     // عطل
                'inspection'     // فحص
            ])->default('preventive');
            
            $table->date('scheduled_date');
            $table->date('actual_date')->nullable();
            $table->text('description');
            $table->text('work_done')->nullable();
            
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('parts_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            
            $table->string('service_provider')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('meter_reading', 10, 2)->nullable();
            
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance');
    }
};
