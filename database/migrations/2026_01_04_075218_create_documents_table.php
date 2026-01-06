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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->string('document_name');
            $table->enum('document_type', ['contract', 'drawing', 'specification', 'certificate', 'report', 'correspondence', 'other']);
            $table->string('category')->nullable();
            $table->string('related_entity_type')->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->string('version')->default('1.0');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->foreignId('uploaded_by_id')->constrained('users');
            $table->enum('status', ['draft', 'review', 'approved', 'archived', 'obsolete'])->default('draft');
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['document_type', 'status']);
            $table->index(['company_id', 'status']);
            $table->index('related_entity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
