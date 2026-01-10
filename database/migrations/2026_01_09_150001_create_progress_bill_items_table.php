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
        Schema::create('progress_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('boq_item_id')->nullable()->constrained('boq_items')->onDelete('set null');
            $table->string('item_code');
            $table->text('description');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            
            // Contract quantities and rates
            $table->decimal('contract_quantity', 15, 4)->default(0);
            $table->decimal('contract_rate', 15, 4)->default(0);
            $table->decimal('contract_amount', 18, 2)->default(0);
            
            // Previous bill
            $table->decimal('previous_quantity', 15, 4)->default(0);
            $table->decimal('previous_amount', 18, 2)->default(0);
            
            // Current bill
            $table->decimal('current_quantity', 15, 4)->default(0);
            $table->decimal('current_amount', 18, 2)->default(0);
            
            // Cumulative
            $table->decimal('cumulative_quantity', 15, 4)->default(0);
            $table->decimal('cumulative_amount', 18, 2)->default(0);
            
            // Progress
            $table->decimal('percentage_complete', 5, 2)->default(0);
            
            // Remaining
            $table->decimal('remaining_quantity', 15, 4)->default(0);
            $table->decimal('remaining_amount', 18, 2)->default(0);
            
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
        Schema::dropIfExists('progress_bill_items');
    }
};
