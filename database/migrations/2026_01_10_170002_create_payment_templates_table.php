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
        Schema::create('payment_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('type', ['check', 'promissory_note', 'guarantee', 'receipt']);
            $table->string('category')->nullable();
            $table->longText('content');
            $table->json('variables')->nullable();
            $table->text('styles')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('cascade');
            $table->enum('language', ['ar', 'en', 'both'])->default('ar');
            $table->string('paper_size')->default('A4');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->json('margins')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('category');
            $table->index('status');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_templates');
    }
};
