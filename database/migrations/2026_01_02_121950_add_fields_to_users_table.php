<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('avatar');
            $table->string('employee_id')->nullable()->unique()->after('job_title');
            $table->boolean('is_active')->default(true)->after('employee_id');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('language')->default('ar')->after('last_login_at');
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete()->after('language');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'phone',
                'avatar',
                'job_title',
                'employee_id',
                'is_active',
                'last_login_at',
                'language',
                'company_id',
            ]);
        });
    }
};
