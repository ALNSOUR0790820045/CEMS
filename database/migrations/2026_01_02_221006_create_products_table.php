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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code')->unique();
            $table->string('sku')->unique()->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['material', 'service', 'equipment', 'consumable'])->default('material');
            $table->string('category')->nullable();
            $table->string('unit')->default('قطعة');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->string('barcode')->nullable();
            $table->integer('reorder_level')->default(0);
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->nullable();
            $table->boolean('track_inventory')->default(true);
            $table->boolean('has_expiry')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
