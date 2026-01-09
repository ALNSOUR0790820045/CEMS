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
        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->date('grn_date');
            $table->foreignId('purchase_order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('vendor_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->string('delivery_note_number')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('status')->default('draft');
            $table->decimal('total_value', 15, 2)->default(0);
            $table->foreignId('received_by_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('inspected_by_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->text('inspection_notes')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grns');
    }
};
