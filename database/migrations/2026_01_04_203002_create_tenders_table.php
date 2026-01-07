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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number')->unique(); // TND-2026-0001
            $table->string('reference_number')->nullable(); // رقم المناقصة لدى الجهة
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            
            // الجهة المالكة
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->string('client_name')->nullable();
            $table->string('client_contact')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            
            // التصنيف
            $table->enum('type', ['public', 'private', 'limited', 'direct_order'])->default('public');
            $table->enum('category', ['building', 'infrastructure', 'industrial', 'maintenance', 'supply', 'other'])->default('building');
            $table->string('sector')->nullable(); // حكومي، خاص، شبه حكومي
            
            // الموقع
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Saudi Arabia');
            
            // التواريخ
            $table->date('announcement_date')->nullable();
            $table->date('documents_deadline')->nullable(); // آخر موعد لشراء الكراسة
            $table->date('questions_deadline')->nullable();
            $table->date('submission_deadline');
            $table->time('submission_time')->nullable();
            $table->date('opening_date')->nullable();
            $table->date('expected_award_date')->nullable();
            
            // القيم المالية
            $table->decimal('estimated_value', 18, 2)->nullable();
            $table->decimal('our_offer_value', 18, 2)->nullable();
            $table->decimal('winning_value', 18, 2)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->decimal('documents_cost', 10, 2)->default(0); // سعر الكراسة
            $table->decimal('bid_bond_amount', 15, 2)->nullable(); // قيمة الضمان الابتدائي
            $table->decimal('bid_bond_percentage', 5, 2)->nullable();
            
            // الحالة
            $table->enum('status', [
                'identified',      // تم اكتشافها
                'studying',        // قيد الدراسة
                'go',              // قرار المشاركة
                'no_go',           // قرار عدم المشاركة
                'documents_purchased', // تم شراء الكراسة
                'pricing',         // قيد التسعير
                'submitted',       // تم التقديم
                'opened',          // تم فتح المظاريف
                'negotiating',     // قيد التفاوض
                'won',             // فوز
                'lost',            // خسارة
                'cancelled',       // ملغاة
                'converted'        // تم التحويل لمشروع
            ])->default('identified');
            
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // Go/No-Go Decision
            $table->boolean('go_decision')->nullable();
            $table->text('go_decision_notes')->nullable();
            $table->foreignId('go_decided_by')->nullable()->constrained('users');
            $table->timestamp('go_decided_at')->nullable();
            
            // النتيجة
            $table->string('winner_name')->nullable(); // اسم الفائز (إذا لم نفز)
            $table->decimal('winner_value', 18, 2)->nullable();
            $table->text('loss_reason')->nullable();
            
            // الربط
            $table->foreignId('project_id')->nullable()->constrained(); // بعد التحويل
            $table->foreignId('bid_bond_id')->nullable()->constrained('guarantees');
            
            // المسؤولين
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->foreignId('estimator_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
