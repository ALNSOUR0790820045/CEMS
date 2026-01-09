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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('certification_number')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('type', ['license', 'permit', 'certificate', 'registration', 'insurance']);
            $table->enum('category', ['company', 'project', 'employee', 'equipment', 'safety']);
            $table->string('issuing_authority');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->date('renewal_date')->nullable();
            $table->enum('status', ['active', 'expired', 'pending_renewal', 'suspended', 'cancelled'])->default('active');
            $table->string('reference_type')->nullable(); // company, project, employee, equipment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('cost', 18, 2)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->string('attachment_path')->nullable();
            $table->integer('reminder_days')->default(30); // 30, 60, 90
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['expiry_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
