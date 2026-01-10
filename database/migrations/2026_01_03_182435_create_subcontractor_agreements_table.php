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
        Schema::create('subcontractor_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('agreement_number')->unique();
            $table->date('agreement_date');
            
            $table->foreignId('subcontractor_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_id')->nullable()->constrained();
            
            $table->enum('agreement_type', ['lump_sum', 'unit_rate', 'time_material', 'cost_plus']);
            $table->text('scope_of_work');
            
            $table->decimal('contract_value', 15, 2);
            $table->foreignId('currency_id')->constrained();
            
            $table->date('start_date');
            $table->date('end_date');
            
            $table->decimal('retention_percentage', 5, 2)->default(0);
            $table->decimal('advance_payment_percentage', 5, 2)->default(0);
            $table->decimal('advance_payment_amount', 15, 2)->nullable();
            
            $table->text('payment_terms')->nullable();
            $table->decimal('performance_bond_percentage', 5, 2)->nullable();
            $table->decimal('performance_bond_amount', 15, 2)->nullable();
            
            $table->enum('status', ['draft', 'approved', 'active', 'completed', 'terminated', 'cancelled'])->default('draft');
            
            $table->string('attachment_path')->nullable();
            
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_agreements');
    }
};
