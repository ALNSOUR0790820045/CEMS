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
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['main', 'subcontract', 'supply', 'service'])->default('main');
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('currency', 3)->default('SAR');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('signed_date')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('draft');
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
