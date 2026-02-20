<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('tenants') || ! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->foreign('plan_id')->references('id')->on('plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tenants') || ! Schema::hasColumn('tenants', 'plan_id')) {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
        });
    }
};
