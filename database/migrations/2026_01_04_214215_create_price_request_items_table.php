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
        Schema::create('price_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_request_id')->constrained()->cascadeOnDelete();
            $table->string('item_description');
            $table->text('specifications')->nullable();
            $table->string('unit');
            $table->decimal('quantity', 15, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_request_items');
    }
};
