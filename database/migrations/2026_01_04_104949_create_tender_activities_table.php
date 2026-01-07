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
        Schema::create('tender_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->onDelete('cascade');
            $table->string('activity_code', 50)->unique();
            $table->string('activity_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2)->storedAs('quantity * unit_price');
            $table->foreignId('wbs_id')->nullable()->constrained('tender_wbs')->onDelete('set null');
            $table->foreignId('parent_activity_id')->nullable()->constrained('tender_activities')->onDelete('cascade');
            $table->integer('sequence_order')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();

            $table->index('tender_id', 'idx_tender_activities_tender');
            $table->index('wbs_id', 'idx_tender_activities_wbs');
            $table->index('status', 'idx_tender_activities_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_activities');
    }
};
