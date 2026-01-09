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
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('location_code');
            $table->string('location_name');
            $table->enum('location_type', ['zone', 'rack', 'bin', 'shelf'])->default('zone');
            $table->foreignId('parent_location_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
            $table->decimal('capacity', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['warehouse_id', 'location_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
