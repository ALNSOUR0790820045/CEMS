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
        Schema::create('subcontractor_ipc_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcontractor_ipc_id')->constrained()->onDelete('cascade');
            
            $table->text('description');
            $table->foreignId('unit_id')->nullable()->constrained();
            
            $table->decimal('agreement_quantity', 10, 2)->nullable();
            $table->decimal('unit_rate', 12, 2)->nullable();
            
            $table->decimal('previous_quantity', 10, 2)->default(0);
            $table->decimal('current_quantity', 10, 2);
            
            $table->text('notes')->nullable();
            
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_ipc_items');
    }
};
