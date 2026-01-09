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
        Schema::create('main_ipc_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_ipc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('boq_item_id')->constrained(); // ربط بجدول الكميات
            $table->foreignId('wbs_id')->nullable()->constrained('project_wbs');
            
            $table->string('item_code');
            $table->text('description');
            $table->string('unit');
            
            // الكميات
            $table->decimal('contract_quantity', 15, 3)->default(0);
            $table->decimal('previous_quantity', 15, 3)->default(0);
            $table->decimal('current_quantity', 15, 3)->default(0);
            $table->decimal('cumulative_quantity', 15, 3)->default(0);
            
            // الأسعار
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->decimal('cumulative_amount', 15, 2)->default(0);
            
            // نسبة الإنجاز
            $table->decimal('completion_percent', 5, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_ipc_items');
    }
};
