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
        Schema::create('tender_wbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('wbs_code'); // 1.1.1.1.1
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            
            // التسلسل الهرمي (5 مستويات)
            $table->integer('level'); // 1-5
            $table->foreignId('parent_id')->nullable()->constrained('tender_wbs')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            
            // التكلفة المخططة
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('materials_cost', 15, 2)->default(0);
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('equipment_cost', 15, 2)->default(0);
            $table->decimal('subcontractor_cost', 15, 2)->default(0);
            
            // المدة
            $table->integer('estimated_duration_days')->nullable();
            
            // الوزن
            $table->decimal('weight_percentage', 5, 2)->default(0);
            
            // الحالة
            $table->boolean('is_active')->default(true);
            $table->boolean('is_summary')->default(false); // عنصر تجميعي
            
            $table->timestamps();
            
            // Composite unique constraint for wbs_code per tender
            $table->unique(['tender_id', 'wbs_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_wbs');
    }
};
