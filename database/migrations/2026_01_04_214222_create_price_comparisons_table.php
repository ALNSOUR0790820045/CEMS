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
        Schema::create('price_comparisons', function (Blueprint $table) {
            $table->id();
            $table->string('comparison_number')->unique();
            $table->foreignId('price_request_id')->constrained();
            $table->date('comparison_date');
            $table->foreignId('selected_quotation_id')->nullable()->constrained('price_quotations');
            $table->text('selection_justification')->nullable();
            $table->foreignId('prepared_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_comparisons');
    }
};
