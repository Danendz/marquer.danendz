<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('study_subject_id')->nullable();
            $table->string('name', 255);
            $table->enum('timer_mode', ['count_up', 'count_down', 'pomodoro']);
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->integer('planned_duration_seconds')->nullable();
            $table->integer('actual_duration_seconds')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->smallInteger('pomodoro_work_minutes')->nullable()->default(25);
            $table->smallInteger('pomodoro_short_break_minutes')->nullable()->default(5);
            $table->smallInteger('pomodoro_long_break_minutes')->nullable()->default(15);
            $table->smallInteger('pomodoro_cycles')->nullable()->default(4);
            $table->smallInteger('pomodoro_completed_cycles')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'started_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_sessions');
    }
};
