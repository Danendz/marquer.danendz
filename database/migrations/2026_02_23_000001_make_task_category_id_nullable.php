<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_category_id']);
            DB::statement('ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_name_task_category_id_unique');
            $table->unsignedBigInteger('task_category_id')->nullable()->change();
            $table->foreign('task_category_id')->references('id')->on('task_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (DB::table('tasks')->whereNull('task_category_id')->exists()) {
            throw new \RuntimeException('Cannot roll back: tasks with NULL task_category_id exist. Reassign or delete them first.');
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_category_id']);
            $table->unsignedBigInteger('task_category_id')->nullable(false)->change();
            $table->foreign('task_category_id')->references('id')->on('task_categories')->onDelete('cascade');
            $table->unique(['name', 'task_category_id']);
        });
    }
};
