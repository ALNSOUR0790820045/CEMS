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
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->string('event_type'); // budget_exceeded, deadline_approaching, approval_pending, low_stock, contract_expiry, etc
            $table->json('conditions')->nullable();
            $table->string('recipients_type')->default('user'); // user, role, department, all
            $table->json('recipients_ids')->nullable();
            $table->json('channels')->nullable(); // email, sms, push, in_app
            $table->text('message_template')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('event_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
