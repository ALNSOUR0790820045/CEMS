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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('contract_value', 15, 2);
            $table->foreignId('currency_id')->constrained()->onDelete('restrict');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->enum('contract_type', ['lump_sum', 'unit_price', 'cost_plus', 'design_build'])->default('lump_sum');
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
