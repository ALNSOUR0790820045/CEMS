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
            $table->string('tender_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('company_id')->constrained('companies');
            $table->date('submission_date')->nullable();
            $table->date('opening_date')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'awarded', 'cancelled'])->default('draft');
            $table->decimal('budget', 15, 2)->nullable();
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
