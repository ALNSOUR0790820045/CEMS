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
        Schema::create('punch_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_number'); // Auto-generated per list
            $table->foreignId('punch_list_id')->constrained()->cascadeOnDelete();
            
            // Location
            $table->string('location')->nullable();
            $table->string('room_number')->nullable();
            $table->string('grid_reference')->nullable();
            $table->string('element')->nullable();
            
            // Description
            $table->text('description');
            
            // Classification
            $table->enum('category', ['defect', 'incomplete', 'damage', 'missing', 'wrong'])->default('defect');
            $table->enum('severity', ['minor', 'major', 'critical'])->default('minor');
            $table->enum('discipline', ['architectural', 'structural', 'electrical', 'mechanical', 'plumbing', 'fire', 'hvac'])->nullable();
            $table->string('trade')->nullable();
            
            // Responsibility
            $table->string('responsible_party')->nullable();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Photos
            $table->json('photos')->nullable(); // Before photos
            $table->json('completion_photos')->nullable(); // After photos
            
            // Dates
            $table->date('due_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->date('verified_date')->nullable();
            
            // Status
            $table->enum('status', ['open', 'in_progress', 'completed', 'verified', 'rejected', 'disputed'])->default('open');
            $table->text('rejection_reason')->nullable();
            $table->text('completion_remarks')->nullable();
            
            // Verification
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Cost
            $table->decimal('cost_to_rectify', 12, 2)->nullable();
            $table->boolean('back_charge')->default(false);
            
            // Priority
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['punch_list_id', 'status']);
            $table->index('assigned_to_id');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_items');
    }
};
