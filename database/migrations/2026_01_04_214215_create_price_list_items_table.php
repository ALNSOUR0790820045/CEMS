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
        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_list_id')->constrained()->cascadeOnDelete();
            $table->string('item_code');
            $table->string('item_name');
            $table->string('item_name_en')->nullable();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->string('unit');
            $table->decimal('unit_price', 15, 4);
            $table->decimal('min_price', 15, 4)->nullable();
            $table->decimal('max_price', 15, 4)->nullable();
            
            // للمواد
            $table->foreignId('material_id')->nullable()->constrained('materials');
            $table->string('brand')->nullable();
            $table->string('origin')->nullable();
            
            // للعمالة
            $table->foreignId('labor_category_id')->nullable()->constrained('labor_categories');
            $table->enum('labor_rate_type', ['hourly', 'daily', 'monthly'])->nullable();
            
            // للمعدات
            $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories');
            $table->enum('equipment_rate_type', ['hourly', 'daily', 'monthly'])->nullable();
            $table->boolean('includes_operator')->default(false);
            $table->boolean('includes_fuel')->default(false);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['price_list_id', 'item_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
    }
};
