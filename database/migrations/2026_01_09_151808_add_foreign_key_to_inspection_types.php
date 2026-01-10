<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            $table->foreign('default_checklist_id')
                  ->references('id')
                  ->on('inspection_templates')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inspection_types', function (Blueprint $table) {
            $table->dropForeign(['default_checklist_id']);
        });
    }
};
