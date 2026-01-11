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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('equipment_number')->unique(); // EQP-001
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            
            // التصنيف
            $table->foreignId('category_id')->constrained('equipment_categories');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('plate_number')->nullable();
            
            // الملكية
            $table->enum('ownership', ['owned', 'rented', 'leased'])->default('owned');
            $table->string('rental_company')->nullable();
            $table->decimal('rental_rate', 15, 2)->nullable();
            $table->enum('rental_rate_type', ['hourly', 'daily', 'weekly', 'monthly'])->nullable();
            
            // القيم المالية
            $table->decimal('purchase_price', 18, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('current_value', 18, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->default(0); // سعر الساعة
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->decimal('operating_cost_per_hour', 10, 2)->default(0);
            
            // المواصفات
            $table->string('capacity')->nullable();
            $table->string('power')->nullable();
            $table->string('fuel_type')->nullable();
            $table->decimal('fuel_consumption', 10, 2)->nullable(); // لتر/ساعة
            
            // الموقع
            $table->enum('status', [
                'available',      // متاح
                'in_use',         // قيد الاستخدام
                'maintenance',    // صيانة
                'breakdown',      // عطل
                'disposed',       // تم التخلص
                'rented_out'      // مؤجر للغير
            ])->default('available');
            
            $table->foreignId('current_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('current_location')->nullable();
            $table->foreignId('assigned_operator_id')->nullable()->constrained('employees')->nullOnDelete();
            
            // الصيانة
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->integer('maintenance_interval_hours')->nullable();
            $table->decimal('current_hours', 10, 2)->default(0);
            $table->decimal('hours_since_last_maintenance', 10, 2)->default(0);
            
            // التأمين والترخيص
            $table->string('insurance_company')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->date('registration_expiry_date')->nullable();
            
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
