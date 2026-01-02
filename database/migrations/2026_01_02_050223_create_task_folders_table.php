<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    / **
     * Create the `task_folders` table with columns for id, name, user_id, and timestamps.
     *
     * The table includes a composite unique index on `user_id` and `name`.
     */
    public function up(): void
    {
        Schema::create('task_folders', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id');
            $table->unique(['user_id', 'name']);
            $table->timestamps();
        });
    }

    /**
     * Drop the task_folders table if it exists.
     *
     * Reverses the migration by removing the `task_folders` table from the database.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_folders');
    }
};