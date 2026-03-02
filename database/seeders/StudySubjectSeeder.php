<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudySubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Mathematics', 'color' => '#4285F4'],
            ['name' => 'Science', 'color' => '#34A853'],
            ['name' => 'Language', 'color' => '#FBBC05'],
            ['name' => 'History', 'color' => '#EA4335'],
            ['name' => 'Programming', 'color' => '#9C27B0'],
            ['name' => 'Art', 'color' => '#FF9800'],
            ['name' => 'Music', 'color' => '#00BCD4'],
            ['name' => 'General', 'color' => '#607D8B'],
        ];

        DB::table('study_subjects')->insertOrIgnore(
            array_map(
                fn($s) => ['user_id' => null, ...$s, 'created_at' => now(), 'updated_at' => now()],
                $subjects
            )
        );
    }
}
