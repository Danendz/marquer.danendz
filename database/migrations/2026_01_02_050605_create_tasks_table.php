<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
