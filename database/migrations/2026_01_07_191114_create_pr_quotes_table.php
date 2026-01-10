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
        Schema::create('pr_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique(); // QT-YYYY-XXXX
            $table->foreignId('purchase_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained();
            $table->date('quote_date');
            $table->date('validity_date');
            $table->decimal('total_amount', 18, 2);
            $table->foreignId('currency_id')->constrained();
            $table->string('payment_terms')->nullable();
            $table->string('delivery_terms')->nullable();
            $table->enum('status', ['received', 'under_review', 'selected', 'rejected'])->default('received');
            $table->string('attachment_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_quotes');
    }
};
