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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->enum('material_type', ['raw_material', 'finished_goods', 'consumables', 'tools', 'equipment'])->default('raw_material');
            $table->foreignId('category_id')->nullable()->constrained('material_categories')->onDelete('set null');
            $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
            $table->decimal('reorder_level', 10, 2)->nullable();
            $table->decimal('min_stock', 10, 2)->nullable();
            $table->decimal('max_stock', 10, 2)->nullable();
            $table->decimal('standard_cost', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->string('barcode')->nullable();
            $table->string('sku')->nullable();
            $table->json('specifications')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_stockable')->default(true);
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
