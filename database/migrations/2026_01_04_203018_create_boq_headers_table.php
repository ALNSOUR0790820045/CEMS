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
        Schema::create('boq_headers', function (Blueprint $table) {
            $table->id();
            $table->string('boq_number')->unique(); // BOQ-2026-0001
            $table->string('name');
            $table->text('description')->nullable();
            $table->morphs('boqable'); // tender_id or project_id
            $table->enum('type', ['tender', 'contract', 'variation'])->default('tender');
            $table->enum('status', ['draft', 'submitted', 'approved', 'revised'])->default('draft');
            $table->integer('version')->default(1);
            $table->string('currency', 3)->default('SAR');
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('markup_percentage', 5, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('final_amount', 18, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('boq_headers');
    }
};
