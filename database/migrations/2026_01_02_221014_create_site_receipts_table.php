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
        Schema::create('site_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->string('receipt_number')->unique(); // SR-2026-001
            
            // معلومات التوريد
            $table->date('receipt_date');
            $table->time('receipt_time');
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            
            // الموقع والجغرافيا
            $table->decimal('latitude', 10, 8);  // GPS
            $table->decimal('longitude', 11, 8); // GPS
            $table->string('location_name'); // "مشروع X - الموقع"
            $table->timestamp('gps_captured_at'); // timestamp دقيق
            
            // المستندات المرفقة (4 إلزامية)
            $table->string('invoice_document')->nullable(); // 1. الفاتورة الأصلية
            $table->string('delivery_note')->nullable();    // 2. مذكرة التسليم
            $table->string('packing_list')->nullable();     // 3. قائمة التعبئة
            $table->json('quality_certificates')->nullable(); // 4. شهادات الجودة
            
            // الحالة
            $table->enum('status', [
                'draft',
                'pending_verification',  // بانتظار التحقق
                'verified',              // تم التحقق
                'grn_created',          // تم إنشاء GRN
                'rejected'
            ])->default('draft');
            
            // التوقيعات الثلاثية (إلزامية)
            
            // 1. مهندس الموقع
            $table->foreignId('engineer_id')->nullable()->constrained('users');
            $table->text('engineer_signature')->nullable(); // Canvas signature data
            $table->timestamp('engineer_signed_at')->nullable();
            $table->text('engineer_notes')->nullable();
            
            // 2. أمين المخزن
            $table->foreignId('storekeeper_id')->nullable()->constrained('users');
            $table->text('storekeeper_signature')->nullable();
            $table->timestamp('storekeeper_signed_at')->nullable();
            $table->text('storekeeper_notes')->nullable();
            
            // 3. السائق/المورد
            $table->string('driver_signature_name')->nullable();
            $table->text('driver_signature')->nullable();
            $table->timestamp('driver_signed_at')->nullable();
            
            // الربط التلقائي
            $table->foreignId('grn_id')->nullable()->constrained('goods_receipt_notes');
            $table->boolean('auto_grn_created')->default(false);
            $table->timestamp('grn_created_at')->nullable();
            
            // إشعار للمالية
            $table->boolean('finance_notified')->default(false);
            $table->timestamp('finance_notified_at')->nullable();
            $table->enum('payment_status', ['pending', 'ready_for_payment', 'paid'])->default('pending');
            
            $table->text('general_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_receipts');
    }
};
