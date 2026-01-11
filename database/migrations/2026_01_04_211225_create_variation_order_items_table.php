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
        Schema::create('variation_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('boq_item_id')->nullable()->constrained(); // إذا كان بند موجود
            $table->string('item_number');
            $table->text('description');
            $table->string('unit');
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_rate', 15, 4);
            $table->decimal('amount', 18, 2);
            $table->enum('rate_basis', [
                'contract_rate',    // سعر العقد
                'agreed_rate',      // سعر متفق عليه
                'cost_plus',        // التكلفة + هامش
                'lump_sum'          // مقطوعية
            ])->default('contract_rate');
            $table->text('rate_justification')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_order_items');
    }
};
