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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add new fields if they don't exist
            if (!Schema::hasColumn('purchase_orders', 'purchase_requisition_id')) {
                $table->foreignId('purchase_requisition_id')->nullable()->after('vendor_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('purchase_orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('project_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('delivery_address');
            }
            if (!Schema::hasColumn('purchase_orders', 'payment_terms_id')) {
                $table->unsignedBigInteger('payment_terms_id')->nullable()->after('delivery_date');
            }
            if (!Schema::hasColumn('purchase_orders', 'currency_id')) {
                $table->foreignId('currency_id')->nullable()->after('payment_terms_id')->constrained()->onDelete('restrict');
            }
            if (!Schema::hasColumn('purchase_orders', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 4)->default(1)->after('currency_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('exchange_rate');
            }
            if (!Schema::hasColumn('purchase_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('purchase_orders', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_by_id')) {
                $table->foreignId('approved_by_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('purchase_orders', 'terms_and_conditions')) {
                $table->text('terms_and_conditions')->nullable()->after('notes');
            }
            
            // Update status enum to include all required statuses
            $table->string('status')->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $columns = ['purchase_requisition_id', 'delivery_address', 'delivery_date', 
                       'payment_terms_id', 'currency_id', 'exchange_rate', 'subtotal', 
                       'discount_amount', 'tax_amount', 'approved_by_id', 'approved_at', 
                       'sent_at', 'terms_and_conditions'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('purchase_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
