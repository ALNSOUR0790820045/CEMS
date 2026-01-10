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
        Schema::create('progress_bill_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_bill_id')->constrained()->onDelete('cascade');
            $table->enum('attachment_type', [
                'measurement_sheet',
                'photos',
                'calculations',
                'correspondence'
            ]);
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size')->unsigned();
            $table->foreignId('uploaded_by_id')->constrained('users')->onDelete('restrict');
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['progress_bill_id']);
            $table->index(['attachment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_bill_attachments');
    }
};
