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
        Schema::create('boq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_header_id')->constrained()->cascadeOnDelete();
            $table->foreignId('boq_section_id')->constrained()->cascadeOnDelete();
            $table->string('item_number'); // 1.1, 1.2, 2.1
            $table->string('code')->nullable();
            $table->text('description');
            $table->text('description_en')->nullable();
            $table->string('unit'); // m3, m2, kg, no, ls
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_rate', 15, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            
            // تفاصيل التكلفة
            $table->decimal('material_cost', 15, 4)->default(0);
            $table->decimal('labor_cost', 15, 4)->default(0);
            $table->decimal('equipment_cost', 15, 4)->default(0);
            $table->decimal('subcontract_cost', 15, 4)->default(0);
            $table->decimal('overhead_cost', 15, 4)->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0);
            
            // للمستخلصات
            $table->decimal('executed_quantity', 15, 4)->default(0);
            $table->decimal('executed_amount', 18, 2)->default(0);
            $table->decimal('remaining_quantity', 15, 4)->default(0);
            $table->decimal('progress_percentage', 5, 2)->default(0);
            
            $table->foreignId('wbs_id')->nullable()->constrained('project_wbs');
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_items');
    }
};
