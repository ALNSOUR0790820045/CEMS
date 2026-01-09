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
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            
            // Document Information
            $table->enum('document_type', ['commercial_registration', 'tax_certificate', 'license', 'quality_certificate', 'insurance', 'bank_letter', 'other']);
            $table->string('document_name');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            
            // Dates
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Upload Information
            $table->unsignedBigInteger('uploaded_by_id');
            
            // Company (Multi-tenancy)
            $table->unsignedBigInteger('company_id');
            
            $table->timestamps();
            
            // Indexes
            $table->index('vendor_id');
            $table->index('document_type');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
