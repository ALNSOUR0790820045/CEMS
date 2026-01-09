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
        Schema::create('change_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_code')->nullable();
            $table->text('description');
            $table->foreignId('wbs_id')->nullable()->constrained('project_wbs');
            
            // الكميات
            $table->decimal('original_quantity', 15, 3)->default(0);
            $table->decimal('changed_quantity', 15, 3)->default(0);
            $table->decimal('quantity_difference', 15, 3)->default(0);
            $table->string('unit')->nullable();
            
            // الأسعار
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_order_items');
    }
};
