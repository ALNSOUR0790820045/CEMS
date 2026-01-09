<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->decimal('requested_quantity', 10, 2);
            $table->decimal('transferred_quantity', 10, 2)->nullable();
            $table->decimal('received_quantity', 10, 2)->nullable();
            $table->decimal('unit_cost', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('stock_transfer_id');
            $table->index('material_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
    }
};
