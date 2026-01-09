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
        Schema::create('variance_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('analysis_date');
            $table->integer('period_month');
            $table->integer('period_year');
            $table->foreignId('cost_code_id')->constrained()->restrictOnDelete();
            $table->decimal('budgeted_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2);
            $table->decimal('variance_amount', 15, 2);
            $table->decimal('variance_percentage', 5, 2);
            $table->enum('variance_type', ['favorable', 'unfavorable']);
            $table->text('variance_reason')->nullable();
            $table->text('corrective_action')->nullable();
            $table->foreignId('responsible_person_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['identified', 'analyzed', 'action_taken', 'closed'])->default('identified');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'analysis_date']);
            $table->index(['project_id', 'period_year', 'period_month']);
            $table->index(['status', 'variance_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variance_analysis');
    }
};
