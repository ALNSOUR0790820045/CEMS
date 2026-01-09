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
        Schema::create('time_bar_protection_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('entity_type'); // e.g., 'invoice', 'contract', 'payment', etc.
            $table->integer('protection_days')->default(30); // Number of days after which record becomes protected
            $table->enum('protection_type', ['view_only', 'full_lock', 'approval_required'])->default('view_only');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('excluded_roles')->nullable(); // Roles that bypass protection
            $table->json('metadata')->nullable(); // Additional settings
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'entity_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_bar_protection_settings');
    }
};
