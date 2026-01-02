<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `task_folders` database table.
     *
     * Creates a table named `task_folders` with an auto-incrementing `id`,
     * a `name` string column, a `user_id` foreign key column, a unique index on `name`,
     * and `created_at`/`updated_at` timestamp columns.
     */
    public function up(): void
    {
        Schema::create('task_folders', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id');
            $table->unique('name');
            $table->timestamps();
        });
    }

    /**
     * Drop the `task_folders` table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_folders');
    }
};