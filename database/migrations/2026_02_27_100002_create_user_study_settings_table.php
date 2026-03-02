<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_study_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->smallInteger('default_work_minutes')->default(25);
            $table->smallInteger('default_short_break_minutes')->default(5);
            $table->smallInteger('default_long_break_minutes')->default(15);
            $table->smallInteger('default_cycles')->default(4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_study_settings');
    }
};
