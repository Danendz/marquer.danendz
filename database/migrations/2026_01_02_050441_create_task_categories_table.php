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
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_categories');
    }
};
