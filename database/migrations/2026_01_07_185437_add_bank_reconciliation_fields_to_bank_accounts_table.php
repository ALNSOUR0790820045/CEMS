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
        Schema::table('bank_accounts', function (Blueprint $table) {
            // Add new fields for bank reconciliation
            $table->decimal('current_balance', 15, 2)->default(0)->after('balance');
            $table->decimal('bank_balance', 15, 2)->default(0)->after('current_balance');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn(['current_balance', 'bank_balance']);
            $table->dropSoftDeletes();
        });
    }
};
