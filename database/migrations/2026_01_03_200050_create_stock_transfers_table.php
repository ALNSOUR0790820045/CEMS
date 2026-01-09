<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->date('transfer_date');
            $table->foreignId('from_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('to_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->enum('status', ['pending', 'approved', 'in_transit', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index('transfer_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
