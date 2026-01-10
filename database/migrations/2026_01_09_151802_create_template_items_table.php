<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_template_id')->constrained()->cascadeOnDelete();
            $table->string('section')->nullable();
            $table->string('item_number');
            $table->text('description');
            $table->text('description_en')->nullable();
            $table->text('acceptance_criteria')->nullable();
            $table->string('inspection_method')->nullable();
            $table->string('reference_standard')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('inspection_template_id');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_items');
    }
};
