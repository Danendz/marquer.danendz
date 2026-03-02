<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 100);
            $table->string('color', 7);
            $table->timestamps();
        });

        // Two partial unique indexes handle NULL user_id correctly in PostgreSQL:
        // user subjects: unique per (user_id, name) when user_id is set
        DB::statement("CREATE UNIQUE INDEX study_subjects_user_name_unique ON study_subjects (user_id, name) WHERE user_id IS NOT NULL");
        // system subjects: unique by name when user_id is NULL
        DB::statement("CREATE UNIQUE INDEX study_subjects_system_name_unique ON study_subjects (name) WHERE user_id IS NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('study_subjects');
    }
};
