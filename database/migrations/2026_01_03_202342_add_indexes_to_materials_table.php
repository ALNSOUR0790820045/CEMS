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
            // Drop indexes using Laravel's automatic index naming convention
            $table->dropIndex('materials_material_code_index');
            $table->dropIndex('materials_barcode_index');
            $table->dropIndex('materials_sku_index');
            $table->dropIndex('materials_material_type_is_active_index');
        });
    }
};
