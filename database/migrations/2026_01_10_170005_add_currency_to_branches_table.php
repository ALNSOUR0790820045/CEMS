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
        // Add currency fields to branches table if they don't exist
        if (!Schema::hasColumn('branches', 'primary_currency_id')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->foreignId('primary_currency_id')->nullable()->after('is_active')
                    ->constrained('currencies')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('branches', 'secondary_currencies')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->json('secondary_currencies')->nullable()->after('primary_currency_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'primary_currency_id')) {
                $table->dropForeign(['primary_currency_id']);
                $table->dropColumn('primary_currency_id');
            }
            if (Schema::hasColumn('branches', 'secondary_currencies')) {
                $table->dropColumn('secondary_currencies');
            }
        });
    }
};
