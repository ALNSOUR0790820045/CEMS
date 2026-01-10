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
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tender_code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->string('client_name')->nullable();
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->date('submission_date')->nullable();
            $table->date('opening_date')->nullable();
            $table->date('project_start_date')->nullable();
            $table->integer('project_duration_days')->nullable();
            $table->enum('status', ['draft', 'submitted', 'won', 'lost', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
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
