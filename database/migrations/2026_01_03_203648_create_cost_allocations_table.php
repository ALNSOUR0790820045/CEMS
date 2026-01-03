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
        Schema::create('cost_allocations', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->foreignId('cost_center_id')->constrained('cost_centers')->onDelete('cascade');
            $table->foreignId('gl_account_id')->constrained('gl_accounts')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_allocations');
    }
};
