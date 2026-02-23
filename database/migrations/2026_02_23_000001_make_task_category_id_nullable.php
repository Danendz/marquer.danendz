<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_category_id']);
            $table->dropUnique(['name', 'task_category_id']);
            $table->unsignedBigInteger('task_category_id')->nullable()->change();
            $table->foreign('task_category_id')->references('id')->on('task_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_category_id']);
            $table->unsignedBigInteger('task_category_id')->nullable(false)->change();
            $table->foreign('task_category_id')->references('id')->on('task_categories')->onDelete('cascade');
            $table->unique(['name', 'task_category_id']);
        });
    }
};
