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
        Schema::create('cost_plus_overhead_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_plus_contract_id')->constrained();
            $table->foreignId('project_id')->constrained();
            $table->integer('year');
            $table->integer('month');
            
            $table->enum('overhead_type', [
                'admin_salaries',       // رواتب إدارية
                'office_rent',          // إيجار مكتب
                'utilities',            // مرافق
                'insurance',            // تأمين
                'depreciation',         // إهلاك
                'other'
            ]);
            
            $table->string('description');
            $table->decimal('total_overhead', 18, 2);
            $table->decimal('allocation_percentage', 5, 2);
            $table->decimal('allocated_amount', 18, 2);
            $table->string('allocation_basis')->nullable(); // أساس التوزيع
            
            $table->boolean('is_reimbursable')->default(true);
            $table->foreignId('allocated_by')->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_plus_overhead_allocations');
    }
};
