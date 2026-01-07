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
            $table->string('contract_code')->unique(); // CNT-YYYY-XXXX
            $table->string('contract_number'); // client's contract reference number
            
            $table->string('contract_title');
            $table->string('contract_title_en')->nullable();
            
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            
            $table->enum('contract_type', ['lump_sum', 'unit_price', 'cost_plus', 'design_build', 'epc', 'bot']);
            $table->enum('contract_category', ['main_contract', 'subcontract', 'supply', 'service']);
            
            $table->decimal('contract_value', 15, 2);
            $table->foreignId('currency_id')->constrained()->onDelete('restrict');
            
            $table->date('signing_date');
            $table->date('commencement_date');
            $table->date('completion_date');
            $table->integer('contract_duration_days')->nullable();
            
            $table->integer('defects_liability_period')->nullable(); // in days
            $table->decimal('retention_percentage', 5, 2)->default(5.00);
            $table->decimal('advance_payment_percentage', 5, 2)->nullable();
            
            $table->text('payment_terms')->nullable();
            $table->text('penalty_clause')->nullable();
            
            $table->text('scope_of_work')->nullable();
            $table->text('special_conditions')->nullable();
            
            $table->enum('contract_status', ['draft', 'under_negotiation', 'signed', 'active', 'on_hold', 'completed', 'terminated', 'closed'])->default('draft');
            
            $table->decimal('original_contract_value', 15, 2);
            $table->decimal('current_contract_value', 15, 2);
            $table->decimal('total_change_orders_value', 15, 2)->default(0);
            
            $table->foreignId('contract_manager_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->onDelete('restrict');
            
            $table->foreignId('gl_revenue_account_id')->nullable()->constrained('g_l_accounts')->onDelete('set null');
            $table->foreignId('gl_receivable_account_id')->nullable()->constrained('g_l_accounts')->onDelete('set null');
            
            $table->foreignId('parent_contract_id')->nullable()->constrained('contracts')->onDelete('set null');
            
            $table->string('attachment_path')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->boolean('is_active')->default(true);
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
