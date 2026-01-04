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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('asset_name');
            $table->enum('asset_category', ['building', 'equipment', 'vehicle', 'furniture', 'computer', 'other']);
            $table->string('asset_type')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 15, 2);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('custodian_id')->nullable();
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'units_of_production']);
            $table->integer('useful_life_years');
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->enum('status', ['active', 'disposed', 'under_maintenance', 'retired'])->default('active');
            $table->unsignedBigInteger('gl_asset_account_id')->nullable();
            $table->unsignedBigInteger('gl_depreciation_account_id')->nullable();
            $table->unsignedBigInteger('gl_accumulated_depreciation_account_id')->nullable();
            $table->date('warranty_expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
