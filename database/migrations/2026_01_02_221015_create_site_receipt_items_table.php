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
        Schema::create('site_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('po_item_id')->nullable()->constrained('purchase_order_items');
            
            // الكميات
            $table->decimal('ordered_quantity', 15, 3)->default(0); // من PO
            $table->decimal('received_quantity', 15, 3)->default(0); // الكمية المستلمة
            $table->decimal('accepted_quantity', 15, 3)->default(0); // المقبولة
            $table->decimal('rejected_quantity', 15, 3)->default(0); // المرفوضة
            $table->string('unit');
            
            // الحالة
            $table->enum('condition', ['good', 'damaged', 'defective', 'partial'])->default('good');
            $table->text('condition_notes')->nullable();
            
            // معلومات إضافية
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_receipt_items');
    }
};
