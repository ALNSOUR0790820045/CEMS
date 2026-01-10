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
        // correspondence table (المراسلات)
        Schema::create('correspondence', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // OUT-2026-0001 / IN-2026-0001
            $table->enum('type', ['incoming', 'outgoing']); // صادر / وارد
            
            // التصنيف
            $table->enum('category', [
                'letter',           // خطاب
                'memo',             // مذكرة
                'email',            // بريد إلكتروني
                'fax',              // فاكس
                'notice',           // إشعار
                'instruction',      // تعليمات
                'request',          // طلب
                'approval',         // موافقة
                'rejection',        // رفض
                'claim',            // مطالبة
                'variation',        // أمر تغيير
                'payment',          // دفعة
                'contract',         // عقد
                'tender',           // مناقصة
                'report',           // تقرير
                'minutes',          // محضر اجتماع
                'other'             // أخرى
            ])->default('letter');
            
            $table->enum('priority', ['normal', 'urgent', 'very_urgent', 'confidential'])->default('normal');
            
            // التفاصيل
            $table->string('subject');
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            
            // المرسل والمستلم
            $table->string('from_entity'); // الجهة المرسلة
            $table->string('from_person')->nullable();
            $table->string('from_position')->nullable();
            $table->string('to_entity'); // الجهة المستلمة
            $table->string('to_person')->nullable();
            $table->string('to_position')->nullable();
            
            // الربط - using unsignedBigInteger for optional foreign keys
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('tender_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            
            // التواريخ
            $table->date('document_date'); // تاريخ المستند
            $table->date('received_date')->nullable(); // تاريخ الاستلام (للوارد)
            $table->date('sent_date')->nullable(); // تاريخ الإرسال (للصادر)
            $table->date('response_required_date')->nullable(); // تاريخ الرد المطلوب
            $table->date('response_date')->nullable(); // تاريخ الرد الفعلي
            
            // المرجع
            $table->string('their_reference')->nullable(); // رقمهم المرجعي
            $table->foreignId('reply_to_id')->nullable()->constrained('correspondence')->nullOnDelete(); // رد على
            $table->foreignId('parent_id')->nullable()->constrained('correspondence')->nullOnDelete(); // سلسلة المراسلات
            
            // الحالة
            $table->enum('status', [
                'draft',            // مسودة
                'pending_approval', // بانتظار الاعتماد
                'approved',         // معتمد
                'sent',             // مرسل
                'received',         // مستلم
                'pending_response', // بانتظار الرد
                'responded',        // تم الرد
                'closed',           // مغلق
                'cancelled'         // ملغي
            ])->default('draft');
            
            $table->boolean('requires_response')->default(false);
            $table->boolean('is_confidential')->default(false);
            
            // التتبع
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // correspondence_attachments table
        Schema::create('correspondence_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correspondence_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->boolean('is_main_document')->default(false);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });

        // correspondence_distribution table (التوزيع)
        Schema::create('correspondence_distribution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correspondence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->enum('action_type', ['to', 'cc', 'bcc', 'for_action', 'for_info', 'for_approval']);
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // correspondence_actions table (الإجراءات)
        Schema::create('correspondence_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correspondence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('action'); // approved, rejected, forwarded, replied, noted
            $table->text('comments')->nullable();
            $table->foreignId('forwarded_to')->nullable()->constrained('users');
            $table->timestamps();
        });

        // correspondence_templates table (القوالب)
        Schema::create('correspondence_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('type', ['incoming', 'outgoing']);
            $table->string('category');
            $table->string('subject_template');
            $table->text('content_template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // correspondence_registers table (سجلات الصادر والوارد)
        Schema::create('correspondence_registers', function (Blueprint $table) {
            $table->id();
            $table->string('register_number')->unique();
            $table->string('name');
            $table->enum('type', ['incoming', 'outgoing']);
            $table->unsignedBigInteger('project_id')->nullable();
            $table->integer('year');
            $table->integer('last_sequence')->default(0);
            $table->string('prefix');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correspondence_registers');
        Schema::dropIfExists('correspondence_templates');
        Schema::dropIfExists('correspondence_actions');
        Schema::dropIfExists('correspondence_distribution');
        Schema::dropIfExists('correspondence_attachments');
        Schema::dropIfExists('correspondence');
    }
};
