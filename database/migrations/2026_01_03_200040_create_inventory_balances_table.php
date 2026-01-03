<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_on_hand', 10, 2)->default(0);
            $table->decimal('quantity_reserved', 10, 2)->default(0);
            $table->decimal('quantity_available', 10, 2)->storedAs('quantity_on_hand - quantity_reserved');
            $table->decimal('last_cost', 12, 2)->nullable();
            $table->decimal('average_cost', 12, 2)->nullable();
            $table->decimal('total_value', 15, 2)->storedAs('quantity_on_hand * COALESCE(average_cost, 0)');
            $table->date('last_transaction_date')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['material_id', 'warehouse_id']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_balances');
    }
};
