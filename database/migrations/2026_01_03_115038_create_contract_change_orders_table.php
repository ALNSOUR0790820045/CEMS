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
        Schema::create('contract_change_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            
            $table->string('change_order_number'); // CO-001, CO-002
            $table->string('change_order_code')->unique(); // CO-CNT-XXXX-001
            
            $table->string('title');
            $table->text('description');
            $table->text('reason');
            
            $table->enum('change_type', ['addition', 'deduction', 'modification', 'time_extension']);
            
            $table->enum('financial_impact', ['increase', 'decrease', 'no_change'])->default('no_change');
            $table->decimal('value_change', 15, 2)->default(0);
            
            $table->enum('time_impact', ['extension', 'reduction', 'no_change'])->default('no_change');
            $table->integer('days_change')->default(0);
            
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'implemented'])->default('draft');
            
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('implementation_date')->nullable();
            
            $table->foreignId('submitted_by_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('restrict');
            
            $table->string('attachment_path')->nullable();
            
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
        Schema::dropIfExists('contract_change_orders');
    }
};
