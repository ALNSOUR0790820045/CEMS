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
        Schema::create('dsi_indices', function (Blueprint $table) {
            $table->id();
            $table->date('index_date'); // شهر المؤشر
            $table->year('year');
            $table->integer('month');
            
            // المؤشرات
            $table->decimal('materials_index', 10, 4); // L (مواد البناء)
            $table->decimal('labor_index', 10, 4);     // P (الأجور)
            $table->decimal('general_index', 10, 4)->nullable(); // مؤشر عام
            
            // معدلات التغير
            $table->decimal('materials_change_percent', 5, 2)->nullable();
            $table->decimal('labor_change_percent', 5, 2)->nullable();
            
            // المصدر
            $table->string('source')->default('DOS Jordan'); // دائرة الإحصاءات العامة
            $table->string('reference_url')->nullable();
            
            $table->timestamps();
            
            $table->unique(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dsi_indices');
    }
};
