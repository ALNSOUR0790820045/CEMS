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
        Schema::create('tender_boq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('item_code');
            $table->string('description');
            $table->string('description_en')->nullable();
            $table->string('unit');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_boq_items');
    }
};
