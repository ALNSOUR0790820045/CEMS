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
        Schema::create('report_history', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->json('report_parameters')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('file_format', ['pdf', 'excel', 'csv']);
            $table->foreignId('generated_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_history');
    }
};
