<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `tasks` table with its columns, keys, and indexes.
     *
     * The table includes an auto-increment `id`, `name` string, `status` enum
     * (`draft`, `progress`, `done`, `cancelled`), `task_category_id` foreign key
     * (constrained, cascade on delete), `user_id` foreign key, a unique composite
     * constraint on (`name`, `task_category_id`), an index on `status`, and
     * timestamp columns.
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
     *
     * Removes the table along with its columns, indexes, and foreign key constraints.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};