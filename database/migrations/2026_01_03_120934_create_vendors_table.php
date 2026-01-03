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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code')->unique();
            
            // Names
            $table->string('name');
            $table->string('name_en')->nullable();
            
            // Classification
            $table->enum('vendor_type', ['materials_supplier', 'equipment_supplier', 'services_provider', 'subcontractor', 'consultant']);
            $table->enum('vendor_category', ['strategic', 'preferred', 'regular', 'blacklisted'])->default('regular');
            
            // Legal Information
            $table->string('commercial_registration')->nullable();
            $table->string('tax_number')->nullable()->unique();
            $table->string('license_number')->nullable();
            
            // Address Information
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
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
            $table->enum('payment_terms', ['cod', '7_days', '15_days', '30_days', '45_days', '60_days', '90_days', 'custom'])->default('30_days');
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            
            // Ratings
            $table->enum('rating', ['excellent', 'good', 'average', 'poor'])->nullable();
            $table->integer('quality_rating')->nullable(); // 1-5
            $table->integer('delivery_rating')->nullable(); // 1-5
            $table->integer('service_rating')->nullable(); // 1-5
            
            // GL Account
            $table->unsignedBigInteger('gl_account_id')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            // Company (Multi-tenancy)
            $table->unsignedBigInteger('company_id');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('vendor_code');
            $table->index('vendor_type');
            $table->index('vendor_category');
            $table->index('is_active');
            $table->index('is_approved');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
