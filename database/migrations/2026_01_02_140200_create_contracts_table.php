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
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_id')->nullable()->constrained();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('contract_value', 15, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('draft');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['project_id', 'status']);
            $table->index('created_at');
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
