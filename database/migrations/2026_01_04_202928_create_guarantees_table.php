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
        Schema::create('guarantees', function (Blueprint $table) {
            $table->id();
            $table->string('guarantee_number')->unique(); // LG-2026-0001
            $table->enum('type', ['bid', 'performance', 'advance_payment', 'maintenance', 'retention']);
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tender_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bank_id')->constrained();
            $table->string('beneficiary'); // الجهة المستفيدة
            $table->string('beneficiary_address')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('SAR');
            $table->decimal('amount_in_base_currency', 15, 2)->nullable();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->date('expected_release_date')->nullable();
            $table->enum('status', ['draft', 'active', 'expired', 'released', 'claimed', 'renewed', 'cancelled'])->default('draft');
            $table->decimal('bank_charges', 10, 2)->default(0);
            $table->decimal('bank_commission_rate', 5, 2)->default(0); // نسبة العمولة السنوية
            $table->decimal('cash_margin', 15, 2)->default(0); // الهامش النقدي
            $table->decimal('margin_percentage', 5, 2)->default(0);
            $table->string('bank_reference_number')->nullable();
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('auto_renewal')->default(false);
            $table->integer('renewal_period_days')->nullable();
            $table->integer('alert_days_before_expiry')->default(30);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantees');
    }
};
