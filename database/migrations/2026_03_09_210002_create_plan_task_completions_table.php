<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_task_id')->constrained('plan_tasks')->cascadeOnDelete();
            $table->date('completed_date');
            $table->timestamps();

            $table->unique(['plan_task_id', 'completed_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_task_completions');
    }
};
