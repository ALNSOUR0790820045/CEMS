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
        Schema::create('contract_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            
            $table->integer('milestone_number');
            $table->string('milestone_name');
            $table->text('description')->nullable();
            
            $table->date('planned_date');
            $table->date('actual_date')->nullable();
            
            $table->decimal('payment_percentage', 5, 2)->nullable(); // % of contract value
            $table->decimal('payment_amount', 15, 2)->nullable();
            
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'delayed', 'cancelled'])->default('not_started');
            $table->decimal('completion_percentage', 5, 2)->default(0);
            
            $table->foreignId('responsible_person_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_milestones');
    }
};
