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
        Schema::create('tender_resource_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_activity_id')->constrained()->cascadeOnDelete();
            
            $table->decimal('assigned_quantity', 10, 2);
            $table->decimal('assigned_hours', 10, 2)->nullable();
            $table->decimal('utilization_percentage', 5, 2)->default(100.00);
            
            $table->decimal('cost', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_resource_assignments');
    }
};
