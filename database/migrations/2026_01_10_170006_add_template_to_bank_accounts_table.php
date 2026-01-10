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
        // Add check template field to bank_accounts table if it doesn't exist
        if (!Schema::hasColumn('bank_accounts', 'check_template_id')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->foreignId('check_template_id')->nullable()->after('currency_id')
                    ->constrained('payment_templates')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('bank_accounts', 'check_template_id')) {
                $table->dropForeign(['check_template_id']);
                $table->dropColumn('check_template_id');
            }
        });
    }
};
