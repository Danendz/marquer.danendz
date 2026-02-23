<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop either variant of the constraint name (live DB vs fresh migration DB)
        DB::statement('ALTER TABLE task_folders DROP CONSTRAINT IF EXISTS task_folders_name_unique');
        DB::statement('ALTER TABLE task_folders DROP CONSTRAINT IF EXISTS task_folders_user_id_name_unique');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE task_folders ADD CONSTRAINT task_folders_user_id_name_unique UNIQUE (user_id, name)');
    }
};
