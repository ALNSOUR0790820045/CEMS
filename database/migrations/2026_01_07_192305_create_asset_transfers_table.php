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
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->foreignId('fixed_asset_id')->constrained()->onDelete('restrict');
            $table->date('transfer_date');
            $table->foreignId('from_location_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');
            $table->foreignId('to_location_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');
            $table->foreignId('from_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('from_project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('to_project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->foreignId('requested_by_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('asset_transfers');
    }
};
