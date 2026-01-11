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
        Schema::create('boq_item_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_item_id')->constrained()->cascadeOnDelete();
            $table->enum('resource_type', ['material', 'labor', 'equipment', 'subcontract']);
            $table->foreignId('resource_id')->nullable(); // material_id, labor_type_id, equipment_id
            $table->string('resource_name');
            $table->string('unit');
            $table->decimal('quantity_per_unit', 15, 6); // الكمية لكل وحدة من البند
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('total_cost', 18, 2);
            $table->decimal('wastage_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_item_resources');
    }
};
