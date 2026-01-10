<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_number')->unique();
            $table->foreignId('inspection_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_type_id')->constrained()->cascadeOnDelete();
            $table->date('inspection_date');
            $table->time('inspection_time')->nullable();
            $table->string('location')->nullable();
            $table->string('work_area')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('witness_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('contractor_rep')->nullable();
            $table->string('consultant_rep')->nullable();
            $table->enum('result', ['pass', 'fail', 'conditional', 'not_applicable'])->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->integer('defects_found')->default(0);
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->foreignId('reinspection_of_id')->nullable()->constrained('inspections')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('project_id');
            $table->index('inspection_type_id');
            $table->index('inspector_id');
            $table->index('inspection_request_id');
            $table->index('status');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
