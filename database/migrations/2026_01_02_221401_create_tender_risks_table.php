<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('risk_code'); // RISK-001
            
            // Make risk_code unique within the tender scope
            $table->unique(['tender_id', 'risk_code']);
            
            // تصنيف المخاطر
            $table->enum('risk_category', [
                'technical',        // فنية
                'financial',        // مالية
                'contractual',      // تعاقدية
                'schedule',         // جدولة
                'resources',        // موارد
                'external',         // خارجية
                'safety',          // سلامة
                'quality',         // جودة
                'political',       // سياسية
                'environmental',   // بيئية
                'other'
            ]);
            
            $table->string('risk_title');
            $table->text('risk_description');
            
            // التقييم
            $table->enum('probability', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->integer('probability_score'); // 1-5
            
            $table->enum('impact', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->integer('impact_score'); // 1-5
            
            $table->integer('risk_score'); // probability × impact (1-25)
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical']); // auto
            
            // التأثير المالي
            $table->decimal('cost_impact_min', 15, 2)->nullable();
            $table->decimal('cost_impact_max', 15, 2)->nullable();
            $table->decimal('cost_impact_expected', 15, 2)->nullable();
            
            // التأثير الزمني
            $table->integer('schedule_impact_days')->nullable();
            
            // استراتيجية الاستجابة
            $table->enum('response_strategy', [
                'avoid',      // تجنب
                'mitigate',   // تخفيف
                'transfer',   // نقل
                'accept'      // قبول
            ])->nullable();
            
            $table->text('response_plan')->nullable();
            $table->decimal('response_cost', 15, 2)->default(0);
            
            // الحالة
            $table->enum('status', ['identified', 'assessed', 'planned', 'monitored', 'closed'])->default('identified');
            
            // المسؤول
            $table->foreignId('owner_id')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_risks');
    }
};
