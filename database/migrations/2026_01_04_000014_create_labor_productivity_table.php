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
        // labor_productivity table (الإنتاجية)
        Schema::create('labor_productivity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('boq_item_id')->nullable()->constrained();
            $table->date('date');
            $table->string('activity_description');
            $table->decimal('quantity_achieved', 15, 4);
            $table->string('unit');
            $table->integer('labor_count');
            $table->decimal('total_hours', 10, 2);
            $table->decimal('productivity_rate', 15, 4); // الكمية لكل ساعة عمل
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_productivity');
    }
};
