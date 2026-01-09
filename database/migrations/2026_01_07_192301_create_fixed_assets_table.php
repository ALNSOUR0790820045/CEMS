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
            $table->string('asset_name_en')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('asset_categories')->onDelete('restrict');
            $table->foreignId('subcategory_id')->nullable()->constrained('asset_categories')->onDelete('restrict');
            $table->string('serial_number')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 15, 2);
            $table->foreignId('currency_id')->constrained()->onDelete('restrict');
            $table->integer('useful_life_years')->nullable();
            $table->integer('useful_life_months')->nullable();
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'units_of_production'])->default('straight_line');
            $table->decimal('depreciation_rate', 5, 2)->nullable()->comment('Percentage');
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->decimal('net_book_value', 15, 2);
            $table->enum('status', ['active', 'disposed', 'sold', 'written_off', 'under_maintenance'])->default('active');
            $table->foreignId('location_id')->nullable()->constrained('warehouse_locations')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->date('warranty_expiry_date')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->foreignId('gl_asset_account_id')->nullable()->constrained('gl_accounts')->onDelete('set null');
            $table->foreignId('gl_depreciation_account_id')->nullable()->constrained('gl_accounts')->onDelete('set null');
            $table->foreignId('gl_accumulated_depreciation_account_id')->nullable()->constrained('gl_accounts')->onDelete('set null');
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
        Schema::dropIfExists('fixed_assets');
    }
};
