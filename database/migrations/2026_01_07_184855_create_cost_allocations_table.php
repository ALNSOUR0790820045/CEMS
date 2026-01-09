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
        Schema::create('cost_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('allocation_number')->unique();
            $table->date('allocation_date');
            $table->foreignId('cost_center_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cost_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('gl_account_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->text('description')->nullable();
            $table->enum('reference_type', ['invoice', 'payroll', 'journal', 'manual'])->default('manual');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
            $table->foreignId('posted_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_allocations');
    }
};
