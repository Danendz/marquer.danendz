<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('end_time');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
