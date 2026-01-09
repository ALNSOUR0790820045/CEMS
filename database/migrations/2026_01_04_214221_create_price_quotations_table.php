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
        Schema::create('price_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_request_id')->constrained();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->string('quotation_number')->nullable();
            $table->date('quotation_date');
            $table->date('validity_date');
            $table->decimal('total_amount', 18, 2);
            $table->string('currency', 3)->default('JOD');
            $table->text('payment_terms')->nullable();
            $table->text('delivery_terms')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_quotations');
    }
};
