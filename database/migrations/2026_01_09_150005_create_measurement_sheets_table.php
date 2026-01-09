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
        Schema::create('measurement_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('boq_item_id')->nullable()->constrained('boq_items')->onDelete('set null');
            $table->string('sheet_number');
            $table->string('location')->nullable();
            $table->text('description');
            $table->decimal('length', 10, 3)->nullable();
            $table->decimal('width', 10, 3)->nullable();
            $table->decimal('height', 10, 3)->nullable();
            $table->decimal('quantity', 15, 4)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('calculated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('checked_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_measured')->nullable();
            $table->json('photos')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['progress_bill_id']);
            $table->index(['boq_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_sheets');
    }
};
