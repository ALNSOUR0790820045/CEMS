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
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_number')->unique(); // PR-YYYY-XXXX
            $table->date('requisition_date');
            $table->date('required_date');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by_id')->constrained('users');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('type', ['materials', 'services', 'equipment', 'subcontract'])->default('materials');
            $table->enum('status', [
                'draft', 
                'pending_approval', 
                'approved', 
                'partially_ordered', 
                'ordered', 
                'rejected', 
                'cancelled'
            ])->default('draft');
            $table->decimal('total_estimated_amount', 18, 2)->default(0);
            $table->foreignId('currency_id')->constrained();
            $table->text('justification')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
