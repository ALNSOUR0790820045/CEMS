<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_template_clauses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('contract_templates')->cascadeOnDelete();
            $table->string('clause_number'); // 1.1, 4.12, 20.1
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('content');
            $table->text('content_en')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('contract_template_clauses');
            $table->integer('sort_order')->default(0);
            
            // معلومات Time Bar
            $table->boolean('has_time_bar')->default(false);
            $table->integer('time_bar_days')->nullable();
            $table->text('time_bar_description')->nullable();
            
            // الفئة
            $table->enum('category', [
                'general',
                'contractor_obligations',
                'employer_obligations',
                'time',
                'payment',
                'variations',
                'claims',
                'termination',
                'disputes',
                'other'
            ])->default('general');
            
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_modifiable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_template_clauses');
    }
};
