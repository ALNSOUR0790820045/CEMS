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
        Schema::create('unforeseeable_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('condition_number')->unique(); // UFC-2026-0001
            $table->foreignId('project_id')->constrained();
            $table->foreignId('contract_id')->nullable()->constrained();
            
            // التفاصيل
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->decimal('location_latitude', 10, 8)->nullable();
            $table->decimal('location_longitude', 11, 8)->nullable();
            
            // نوع الظرف
            $table->enum('condition_type', [
                'ground_conditions',        // ظروف التربة
                'rock_conditions',          // ظروف صخرية
                'water_conditions',         // ظروف مائية
                'contamination',            // تلوث
                'underground_utilities',    // خدمات تحت الأرض
                'archaeological',           // آثار
                'unexploded_ordnance',      // مخلفات حربية
                'other'
            ]);
            
            // التواريخ
            $table->date('discovery_date');
            $table->date('notice_date')->nullable();
            $table->date('inspection_date')->nullable();
            
            // البند التعاقدي
            $table->string('contractual_clause')->default('4.12');
            
            // التأثير
            $table->text('impact_description');
            $table->integer('estimated_delay_days')->default(0);
            $table->decimal('estimated_cost_impact', 18, 2)->default(0);
            $table->string('currency', 3)->default('JOD');
            
            // ما كان يمكن توقعه (للمقارنة)
            $table->text('tender_assumptions')->nullable();
            $table->text('site_investigation_data')->nullable();
            $table->text('actual_conditions');
            $table->text('difference_analysis');
            
            // الإجراءات المتخذة
            $table->text('immediate_measures')->nullable();
            $table->text('proposed_solution')->nullable();
            
            // الحالة
            $table->enum('status', [
                'identified',
                'notice_sent',
                'under_investigation',
                'agreed',
                'disputed',
                'resolved',
                'rejected'
            ])->default('identified');
            
            // الربط
            $table->foreignId('time_bar_event_id')->nullable()->constrained('time_bar_events');
            $table->foreignId('claim_id')->nullable()->constrained('claims');
            $table->foreignId('eot_id')->nullable()->constrained('eot_requests');
            
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('verified_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('unforeseeable_conditions');
    }
};
