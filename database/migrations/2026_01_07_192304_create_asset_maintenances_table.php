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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('maintenance_number')->unique();
            $table->foreignId('fixed_asset_id')->constrained()->onDelete('cascade');
            $table->enum('maintenance_type', ['preventive', 'corrective', 'emergency']);
            $table->date('maintenance_date');
            $table->text('description');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('cost', 15, 2)->default(0);
            $table->boolean('is_capitalized')->default(false)->comment('Whether cost should be added to asset value');
            $table->date('next_maintenance_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->string('performed_by')->nullable();
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
        Schema::dropIfExists('asset_maintenances');
    }
};
