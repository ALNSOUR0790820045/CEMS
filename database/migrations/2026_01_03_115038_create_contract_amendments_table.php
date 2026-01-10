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
        Schema::create('contract_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            
            $table->integer('amendment_number'); // 1, 2, 3
            $table->string('amendment_code')->unique(); // AMD-CNT-XXXX-001
            
            $table->string('title');
            $table->text('description');
            
            $table->date('amendment_date');
            $table->date('effective_date');
            
            $table->decimal('previous_contract_value', 15, 2);
            $table->decimal('new_contract_value', 15, 2);
            $table->decimal('value_difference', 15, 2)->storedAs('new_contract_value - previous_contract_value');
            
            $table->date('previous_completion_date')->nullable();
            $table->date('new_completion_date')->nullable();
            $table->integer('days_extended')->nullable();
            
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'active'])->default('draft');
            
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('approved_at')->nullable();
            
            $table->string('attachment_path')->nullable();
            
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
        Schema::dropIfExists('contract_amendments');
    }
};
