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
        Schema::create('vendor_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('material_id');
            
            // Pricing
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            
            // Lead Time and MOQ
            $table->integer('lead_time_days')->nullable();
            $table->decimal('minimum_order_quantity', 10, 2)->nullable();
            
            // Status
            $table->boolean('is_preferred')->default(false);
            
            // Notes
            $table->text('notes')->nullable();
            
            // Company (Multi-tenancy)
            $table->unsignedBigInteger('company_id');
            
            $table->timestamps();
            
            // Indexes
            $table->index('vendor_id');
            $table->index('material_id');
            $table->index('company_id');
            $table->index('is_preferred');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_materials');
    }
};
