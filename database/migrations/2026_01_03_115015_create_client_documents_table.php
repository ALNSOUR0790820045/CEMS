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
        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Document Information
            $table->enum('document_type', ['commercial_registration', 'tax_certificate', 'license', 'contract', 'id_copy', 'other']);
            $table->string('document_name');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            
            // Dates
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Uploaded By
            $table->foreignId('uploaded_by_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Company
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_documents');
    }
};
