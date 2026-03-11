<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->string('start_time', 5)->nullable()->after('sort_order');
            $table->string('end_time', 5)->nullable()->after('start_time');
        });
    }

    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
