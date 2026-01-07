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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_code')->unique();
            
            // Names
            $table->string('name');
            $table->string('name_en')->nullable();
            
            // Type and Category
            $table->enum('client_type', ['government', 'semi_government', 'private_sector', 'individual']);
            $table->enum('client_category', ['strategic', 'preferred', 'regular', 'one_time']);
            
            // Legal Information
            $table->string('commercial_registration')->nullable();
            $table->string('tax_number')->unique()->nullable();
            $table->string('license_number')->nullable();
            
            // Location - Using string fields since countries/cities tables don't exist yet
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('po_box')->nullable();
            $table->string('postal_code')->nullable();
            
            // Contact Information
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // Primary Contact Person
            $table->string('primary_contact_person')->nullable();
            $table->string('primary_contact_title')->nullable();
            $table->string('primary_contact_phone')->nullable();
            $table->string('primary_contact_email')->nullable();
            
            // Financial Settings
            $table->enum('payment_terms', ['immediate', '7_days', '15_days', '30_days', '45_days', '60_days', '90_days', 'custom'])->default('30_days');
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->string('currency')->default('JOD')->nullable();
            
            // Rating
            $table->enum('rating', ['excellent', 'good', 'average', 'poor'])->nullable();
            
            // GL Account - Using string for now since gl_accounts table doesn't exist
            $table->string('gl_account')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Status and Company
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('clients');
    }
};
