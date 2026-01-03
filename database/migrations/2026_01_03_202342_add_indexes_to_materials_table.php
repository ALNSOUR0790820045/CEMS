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
        Schema::table('materials', function (Blueprint $table) {
            // Add index on material_code for better performance
            $table->index('material_code');
            // Add index on barcode and sku for search operations
            $table->index('barcode');
            $table->index('sku');
            // Add composite index for filtering by type and active status
            $table->index(['material_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['material_code']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['sku']);
            $table->dropIndex(['material_type', 'is_active']);
        });
    }
};
