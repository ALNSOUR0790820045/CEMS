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
        // Tender Procurement Packages
        Schema::create('tender_procurement_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('package_code')->unique(); // PKG-001
            $table->string('package_name');
            $table->text('description')->nullable();
            
            // التصنيف
            $table->enum('procurement_type', [
                'materials',        // مواد
                'equipment',        // معدات
                'subcontract',      // مقاولة فرعية
                'services',         // خدمات
                'rental'           // إيجار
            ]);
            
            $table->enum('category', [
                'civil',           // مدني
                'structural',      // إنشائي
                'architectural',   // معماري
                'electrical',      // كهربائي
                'mechanical',      // ميكانيكي
                'plumbing',        // صحي
                'finishing',       // تشطيبات
                'other'
            ])->nullable();
            
            // الكميات والتكلفة
            $table->text('scope_of_work')->nullable();
            $table->json('quantities')->nullable(); // قائمة البنود
            $table->decimal('estimated_value', 15, 2)->default(0);
            
            // الجدول الزمني
            $table->date('required_by_date')->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->date('procurement_start')->nullable();
            
            // استراتيجية الشراء
            $table->enum('strategy', [
                'competitive_bidding',  // منافسة
                'direct_purchase',      // شراء مباشر
                'framework_agreement',  // اتفاقية إطار
                'preferred_supplier'    // مورد مفضل
            ])->default('competitive_bidding');
            
            // المتطلبات
            $table->boolean('requires_technical_specs')->default(true);
            $table->boolean('requires_samples')->default(false);
            $table->boolean('requires_warranty')->default(false);
            $table->integer('warranty_months')->nullable();
            
            // الحالة
            $table->enum('status', [
                'planned',
                'rfq_prepared',
                'quotations_received',
                'evaluated',
                'approved'
            ])->default('planned');
            
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // الموردون المحتملون
        Schema::create('tender_procurement_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_procurement_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained();
            
            $table->decimal('quoted_price', 15, 2)->nullable();
            $table->integer('delivery_days')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('technical_compliance')->nullable();
            
            $table->integer('score')->nullable(); // 0-100
            $table->boolean('is_recommended')->default(false);
            
            $table->timestamps();
        });

        // Long Lead Items
        Schema::create('tender_long_lead_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_procurement_package_id')->nullable()->constrained();
            
            $table->string('item_name');
            $table->text('description');
            $table->integer('lead_time_weeks');
            $table->date('must_order_by');
            $table->decimal('estimated_cost', 15, 2);
            
            $table->boolean('is_critical')->default(false);
            $table->text('mitigation_plan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_long_lead_items');
        Schema::dropIfExists('tender_procurement_suppliers');
        Schema::dropIfExists('tender_procurement_packages');
    }
};
