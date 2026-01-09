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
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Contact Information
            $table->string('full_name');
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            
            // Contact Details
            $table->string('phone')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            
            // Status
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Notes
            $table->text('notes')->nullable();
            
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
        Schema::dropIfExists('client_contacts');
    }
};
