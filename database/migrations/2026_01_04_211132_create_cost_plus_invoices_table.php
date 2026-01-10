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
        Schema::create('cost_plus_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('cost_plus_contract_id')->constrained();
            $table->foreignId('project_id')->constrained();
            
            $table->date('invoice_date');
            $table->date('period_from');
            $table->date('period_to');
            
            // التكاليف
            $table->decimal('material_costs', 18, 2)->default(0);
            $table->decimal('labor_costs', 18, 2)->default(0);
            $table->decimal('equipment_costs', 18, 2)->default(0);
            $table->decimal('subcontract_costs', 18, 2)->default(0);
            $table->decimal('overhead_costs', 18, 2)->default(0);
            $table->decimal('other_costs', 18, 2)->default(0);
            $table->decimal('total_direct_costs', 18, 2)->default(0);
            
            // الربح
            $table->decimal('fee_amount', 18, 2)->default(0);
            $table->decimal('incentive_amount', 18, 2)->default(0);
            
            // الإجمالي
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('vat_percentage', 5, 2)->default(16);
            $table->decimal('vat_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->string('currency', 3)->default('JOD');
            
            // GMP Check
            $table->decimal('cumulative_costs', 18, 2)->default(0);
            $table->decimal('gmp_remaining', 18, 2)->nullable();
            $table->boolean('gmp_exceeded')->default(false);
            
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'rejected'])->default('draft');
            
            $table->foreignId('prepared_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_plus_invoices');
    }
};
