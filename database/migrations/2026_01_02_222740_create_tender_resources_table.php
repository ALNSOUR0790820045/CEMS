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
        Schema::create('tender_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('resource_code')->unique(); // TR-001
            $table->string('name');
            $table->string('name_en')->nullable();
            
            // التصنيف
            $table->enum('resource_type', [
                'labor',            // عمالة
                'equipment',        // معدات
                'material',         // مواد
                'subcontractor',    // مقاول فرعي
                'consultant'        // استشاري
            ]);
            
            $table->string('category')->nullable(); // مهندس، فني، عامل...
            $table->string('skill_level')->nullable(); // خبير، متوسط، مبتدئ
            
            // التكلفة
            $table->decimal('unit_cost', 15, 2)->default(0); // يومي/شهري
            $table->enum('cost_unit', ['hour', 'day', 'month', 'unit'])->default('day');
            
            // الكمية المطلوبة
            $table->decimal('required_quantity', 10, 2)->default(0);
            $table->string('quantity_unit')->nullable();
            
            // التوافر
            $table->boolean('is_available')->default(true);
            $table->date('available_from')->nullable();
            $table->date('available_to')->nullable();
            
            // التكلفة الإجمالية
            $table->decimal('total_cost', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_resources');
    }
};
