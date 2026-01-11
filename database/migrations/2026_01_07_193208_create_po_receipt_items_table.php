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
        Schema::create('po_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_receipt_id')->constrained()->onDelete('cascade');
            $table->foreignId('po_item_id')->constrained('purchase_order_items')->onDelete('restrict');
            $table->decimal('quantity_received', 10, 2);
            $table->decimal('quantity_accepted', 10, 2)->default(0);
            $table->decimal('quantity_rejected', 10, 2)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->text('inspection_notes')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_receipt_items');
    }
};
