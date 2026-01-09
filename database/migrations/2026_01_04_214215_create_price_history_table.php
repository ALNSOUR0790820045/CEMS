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
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_list_item_id')->constrained()->cascadeOnDelete();
            $table->date('effective_date');
            $table->decimal('old_price', 15, 4)->nullable();
            $table->decimal('new_price', 15, 4);
            $table->decimal('change_percentage', 8, 4)->nullable();
            $table->enum('change_reason', [
                'market_change',    // تغير السوق
                'supplier_update',  // تحديث المورد
                'inflation',        // تضخم
                'currency',         // سعر الصرف
                'seasonal',         // موسمي
                'other'
            ])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_history');
    }
};
