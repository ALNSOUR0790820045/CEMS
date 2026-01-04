<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compliance_requirement_id')->constrained('compliance_requirements')->cascadeOnDelete();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->date('due_date');
            $table->date('completion_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue', 'waived'])->default('pending');
            $table->foreignId('responsible_person_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('evidence_file_path')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            // Index for polymorphic relationship
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_tracking');
    }
};
