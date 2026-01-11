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
        Schema::create('diary_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_diary_id')->constrained('site_diaries')->onDelete('cascade');
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('quantity_received', 15, 2)->default(0.00);
            $table->decimal('quantity_used', 15, 2)->default(0.00);
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('delivery_note_number')->nullable();
            $table->string('location_used')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_materials');
    }
};
