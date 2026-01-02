<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the "tasks" table used to store task records.
     *
     * The table includes:
     * - `id`: auto-incrementing primary key.
     * - `name`: string column.
     * - `status`: enum with values `draft`, `progress`, `done`, `cancelled`.
     * - `task_category_id`: foreign key constrained to the referenced table and set to cascade on delete.
     * - `user_id`: foreign id column.
     * - composite unique index on (`name`, `task_category_id`).
     * - index on `status`.
     * - `created_at` and `updated_at` timestamp columns.
     */
    public function up(): void
    {
        Schema::create('tasks', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['draft', 'progress', 'done', 'cancelled']);
            $table->foreignId('task_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->unique(['name', 'task_category_id']);
            $table->index('status');
            $table->timestamps();
        });
    }

    /**
     * Drop the 'tasks' table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};