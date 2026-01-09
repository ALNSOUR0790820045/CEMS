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
        Schema::create('progress_bill_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('variation_order_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description');
            $table->decimal('quantity', 15, 4)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('rate', 15, 4)->default(0);
            $table->decimal('previous_amount', 18, 2)->default(0);
            $table->decimal('current_amount', 18, 2)->default(0);
            $table->decimal('cumulative_amount', 18, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['progress_bill_id']);
            $table->index(['variation_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_bill_variations');
    }
};
