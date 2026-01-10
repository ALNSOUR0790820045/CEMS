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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            
            // Personal Information (Arabic)
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            
            // Personal Information (English)
            $table->string('first_name_en')->nullable();
            $table->string('middle_name_en')->nullable();
            $table->string('last_name_en')->nullable();
            
            // Identification
            $table->string('national_id')->unique()->nullable();
            $table->string('passport_number')->unique()->nullable();
            
            // Birth Information
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->foreignId('nationality_id')->nullable()->constrained('countries')->nullOnDelete();
            
            // Personal Status
            $table->enum('gender', ['male', 'female']);
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            
            // Contact Information
            $table->string('mobile');
            $table->string('phone')->nullable();
            $table->string('email')->unique()->nullable();
            
            // Address
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->text('address')->nullable();
            
            // Employment Information
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('job_title');
            
            // Employment Type & Status
            $table->enum('employee_type', ['permanent', 'contract', 'temporary', 'consultant', 'daily_worker'])->default('permanent');
            $table->enum('employment_status', ['active', 'on_leave', 'suspended', 'resigned', 'terminated'])->default('active');
            
            // Employment Dates
            $table->date('hire_date');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('probation_end_date')->nullable();
            
            // Termination Information
            $table->date('resignation_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();
            
            // Compensation
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->enum('payment_frequency', ['monthly', 'daily', 'hourly'])->default('monthly');
            
            // Banking Information
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('bank_account_number')->nullable();
            $table->string('iban')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // Legal Documents
            $table->string('visa_number')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->string('work_permit_number')->nullable();
            $table->date('work_permit_expiry_date')->nullable();
            $table->string('health_insurance_number')->nullable();
            $table->date('health_insurance_expiry_date')->nullable();
            
            // Supervisor
            $table->foreignId('supervisor_id')->nullable()->constrained('employees')->nullOnDelete();
            
            // Additional Information
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            
            // Status & User Link
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Company (Multi-tenancy)
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
