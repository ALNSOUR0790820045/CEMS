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
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->id();
            $table->string('subcontractor_code')->unique();
            
            // Basic Information
            $table->string('name');
            $table->string('name_en')->nullable();
            
            // Classification
            $table->enum('subcontractor_type', ['specialized', 'general', 'labor_only', 'materials_labor']);
            $table->enum('trade_category', ['civil', 'electrical', 'mechanical', 'plumbing', 'finishing', 'landscaping', 'other']);
            
            // Legal Information
            $table->string('commercial_registration')->nullable();
            $table->string('tax_number')->nullable()->unique();
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            
            // Location
            $table->foreignId('country_id')->nullable()->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
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
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_title')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_email')->nullable();
            
            // Financial Terms
            $table->enum('payment_terms', ['cod', '7_days', '15_days', '30_days', '45_days', '60_days'])->default('30_days');
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained();
            $table->decimal('retention_percentage', 5, 2)->default(0);
            
            // Insurance
            $table->string('insurance_certificate_number')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->decimal('insurance_value', 15, 2)->nullable();
            
            // Performance Ratings
            $table->enum('rating', ['excellent', 'good', 'average', 'poor'])->nullable();
            $table->integer('quality_rating')->nullable();
            $table->integer('time_performance_rating')->nullable();
            $table->integer('safety_rating')->nullable();
            
            // Accounting
            $table->foreignId('gl_account_id')->nullable()->constrained();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            
            // Approval
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Audit
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractors');
    }
};
