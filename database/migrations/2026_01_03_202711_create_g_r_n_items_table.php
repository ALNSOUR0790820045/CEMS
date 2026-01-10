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
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained('grns')->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('material_id')->constrained()->onDelete('restrict');
            $table->decimal('ordered_quantity', 10, 2)->nullable();
            $table->decimal('received_quantity', 10, 2);
            $table->decimal('accepted_quantity', 10, 2)->default(0);
            $table->decimal('rejected_quantity', 10, 2)->default(0);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_amount', 15, 2);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('inspection_status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};
