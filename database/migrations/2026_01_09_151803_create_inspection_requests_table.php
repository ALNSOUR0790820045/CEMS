<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspection_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by_id')->constrained('users')->cascadeOnDelete();
            $table->date('request_date');
            $table->date('requested_date');
            $table->time('requested_time')->nullable();
            $table->string('location')->nullable();
            $table->string('work_area')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('boq_item_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('project_id');
            $table->index('inspection_type_id');
            $table->index('requested_by_id');
            $table->index('inspector_id');
            $table->index('status');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_requests');
    }
};
