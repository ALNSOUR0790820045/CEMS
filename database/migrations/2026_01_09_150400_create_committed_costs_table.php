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
        Schema::create('committed_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cost_code_id')->constrained()->restrictOnDelete();
            $table->foreignId('budget_item_id')->nullable()->constrained('project_budget_items')->nullOnDelete();
            $table->enum('commitment_type', ['purchase_order', 'subcontract', 'service_order']);
            $table->unsignedBigInteger('commitment_id');
            $table->string('commitment_number');
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->text('description');
            $table->decimal('original_amount', 15, 2);
            $table->decimal('approved_changes', 15, 2)->default(0);
            $table->decimal('current_amount', 15, 2);
            $table->decimal('invoiced_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['open', 'partially_invoiced', 'closed', 'cancelled'])->default('open');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['commitment_type', 'commitment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committed_costs');
    }
};
