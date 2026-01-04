<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('certification_code')->unique();
            $table->string('certification_name');
            $table->enum('certification_type', ['company', 'employee', 'equipment', 'material', 'contractor']);
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('issuing_authority');
            $table->string('certificate_number')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->boolean('is_renewable')->default(true);
            $table->integer('renewal_period_days')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'renewed'])->default('active');
            $table->string('certificate_file_path')->nullable();
            $table->integer('alert_before_days')->default(30);
            $table->date('last_alert_sent')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Index for polymorphic relationship
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
