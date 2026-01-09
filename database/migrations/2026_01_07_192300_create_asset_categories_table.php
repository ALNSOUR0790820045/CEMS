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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('asset_categories')->onDelete('cascade');
            $table->integer('default_useful_life')->nullable()->comment('In months');
            $table->enum('default_depreciation_method', ['straight_line', 'declining_balance', 'units_of_production'])->default('straight_line');
            $table->decimal('default_depreciation_rate', 5, 2)->nullable()->comment('Percentage');
            $table->foreignId('gl_asset_account_id')->nullable()->constrained('gl_accounts')->onDelete('set null');
            $table->foreignId('gl_depreciation_account_id')->nullable()->constrained('gl_accounts')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
