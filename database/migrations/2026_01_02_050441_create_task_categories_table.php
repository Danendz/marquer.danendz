<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
         * Create the `task_categories` table with its columns and constraints.
         *
         * The table includes:
         * - `id` (auto-increment primary key)
         * - `name` (string)
         * - `color` (string)
         * - `task_folder_id` (unsigned big integer, foreign key to `task_folders` with cascade on delete)
         * - `user_id` (unsigned big integer)
         * - composite unique index on (`name`, `task_folder_id`)
         * - `created_at` and `updated_at` timestamps
         */
    public function up(): void
    {
        Schema::create('task_categories', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            $table->foreignId('task_folder_id')->constrained('task_folders')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->unique(['name', 'task_folder_id']);
            $table->timestamps();
        });
    }

    /**
     * Drop the `task_categories` table if it exists.
     *
     * Removes the `task_categories` table from the database, undoing the migration that created it.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_categories');
    }
};