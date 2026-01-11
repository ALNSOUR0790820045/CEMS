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
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('tender_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'closed', 'awarded', 'cancelled'])->default('draft');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('status');
            $table->index('issue_date');
            $table->index('closing_date');
            $table->index('created_at');
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
