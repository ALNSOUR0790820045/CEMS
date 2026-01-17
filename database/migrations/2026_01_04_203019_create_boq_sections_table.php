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
        Schema::create('boq_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_header_id')->constrained()->cascadeOnDelete();
            $table->string('code'); // A, B, C or 1, 2, 3
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_sections');
    }
};
