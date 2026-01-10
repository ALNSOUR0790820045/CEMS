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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            // Make material_id nullable
            $table->foreignId('material_id')->nullable()->change();
            
            // Add new fields if they don't exist
            if (!Schema::hasColumn('purchase_order_items', 'description')) {
                $table->text('description')->nullable()->after('material_id');
            }
            if (!Schema::hasColumn('purchase_order_items', 'specifications')) {
                $table->text('specifications')->nullable()->after('description');
            }
            if (!Schema::hasColumn('purchase_order_items', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('quantity')->constrained()->onDelete('restrict');
            }
            if (!Schema::hasColumn('purchase_order_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('purchase_order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percentage');
            }
            if (!Schema::hasColumn('purchase_order_items', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('purchase_order_items', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percentage');
            }
            if (!Schema::hasColumn('purchase_order_items', 'total_price')) {
                $table->decimal('total_price', 15, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('purchase_order_items', 'quantity_received')) {
                $table->decimal('quantity_received', 10, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('purchase_order_items', 'quantity_invoiced')) {
                $table->decimal('quantity_invoiced', 10, 2)->default(0)->after('quantity_received');
            }
            if (!Schema::hasColumn('purchase_order_items', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('quantity_invoiced');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $columns = ['description', 'specifications', 'unit_id', 'discount_percentage', 
                       'discount_amount', 'tax_percentage', 'tax_amount', 'total_price', 
                       'quantity_received', 'quantity_invoiced', 'delivery_date'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('purchase_order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
