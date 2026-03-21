<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('theme', 10)->default('light');
            $table->string('language', 5)->default('en');
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('notification_study_reminders')->default(true);
            $table->boolean('notification_task_reminders')->default(true);
            $table->json('lab_features')->default('{}');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
