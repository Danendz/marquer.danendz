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
        Schema::create('app_releases', function (Blueprint $table) {
            $table->id();

            $table->string('platform', 20);
            $table->string('channel', 20)->default('stable');

            $table->string('version', 32);
            $table->unsignedInteger('build_number')->nullable();
            $table->string('version_full', 64)->nullable();

            $table->string('git_sha', 40)->nullable();

            $table->string('bucket')->nullable();
            $table->string('object_key_latest')->nullable();
            $table->string('object_key_commit')->nullable();

            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->unique(['platform', 'channel', 'version'], 'app_releases_platform_channel_version_unique');
            $table->index(['platform', 'channel', 'released_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_releases');
    }
};
