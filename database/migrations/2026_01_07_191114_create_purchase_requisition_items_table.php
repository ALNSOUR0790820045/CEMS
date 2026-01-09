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
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained();
            $table->string('item_description');
            $table->text('specifications')->nullable();
            $table->decimal('quantity_requested', 15, 3);
            $table->foreignId('unit_id')->constrained();
            $table->decimal('estimated_unit_price', 18, 2)->default(0);
            $table->decimal('estimated_total', 18, 2)->default(0);
            $table->decimal('quantity_ordered', 15, 3)->default(0);
            $table->foreignId('preferred_vendor_id')->nullable()->constrained('vendors');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
