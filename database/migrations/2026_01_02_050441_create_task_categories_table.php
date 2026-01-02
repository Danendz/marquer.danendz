<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `task_categories` table with its columns and constraints.
     *
     * Creates columns: `id`, `name`, `color`, `task_folder_id`, `user_id`, `created_at`, and `updated_at`.
     * Adds a foreign key on `task_folder_id` referencing `task_folders.id` with cascade-on-delete and a unique constraint on the combination of `name` and `task_folder_id`.
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
     */
    public function down(): void
    {
        Schema::dropIfExists('task_categories');
    }
};