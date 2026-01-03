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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('project_type', ['lump_sum', 'unit_price', 'cost_plus', 'design_build'])->default('lump_sum');
            $table->enum('project_status', ['tendering', 'awarded', 'mobilization', 'execution', 'on_hold', 'completed', 'closed'])->default('tendering');
            
            $table->decimal('contract_value', 15, 2);
            $table->foreignId('contract_currency_id')->constrained('currencies')->onDelete('restrict');
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->integer('contract_duration_days');
            
            $table->string('location')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->text('site_address')->nullable();
            $table->decimal('gps_latitude', 10, 8)->nullable();
            $table->decimal('gps_longitude', 11, 8)->nullable();
            
            $table->foreignId('project_manager_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('site_engineer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('contract_manager_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('projects');
    }
};
