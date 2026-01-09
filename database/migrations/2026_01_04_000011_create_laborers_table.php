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
        // laborers table (العمال)
        Schema::create('laborers', function (Blueprint $table) {
            $table->id();
            $table->string('labor_number')->unique(); // LBR-0001
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('category_id')->constrained('labor_categories');
            
            // المعلومات الشخصية
            $table->string('nationality')->nullable();
            $table->string('id_number')->nullable(); // رقم الإقامة/الهوية
            $table->date('id_expiry_date')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            
            // التوظيف
            $table->enum('employment_type', ['permanent', 'temporary', 'subcontractor'])->default('permanent');
            $table->foreignId('subcontractor_id')->nullable()->constrained('subcontractors');
            $table->date('joining_date');
            $table->date('contract_end_date')->nullable();
            $table->decimal('daily_wage', 10, 2);
            $table->decimal('overtime_rate', 10, 2)->nullable();
            
            // الموقع الحالي
            $table->enum('status', ['available', 'assigned', 'on_leave', 'sick', 'terminated'])->default('available');
            $table->foreignId('current_project_id')->nullable()->constrained('projects');
            $table->string('current_location')->nullable();
            
            // التدريب والسلامة
            $table->boolean('safety_trained')->default(false);
            $table->date('safety_training_date')->nullable();
            $table->date('safety_training_expiry')->nullable();
            $table->boolean('medical_checked')->default(false);
            $table->date('medical_check_date')->nullable();
            
            $table->string('photo')->nullable();
            $table->text('skills')->nullable();
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
        Schema::dropIfExists('laborers');
    }
};
