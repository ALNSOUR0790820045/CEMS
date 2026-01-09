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
        Schema::create('tender_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // technical, financial, legal, drawing, specification
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->enum('source', ['received', 'prepared'])->default('received');
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_documents');
    }
};
