<?php

namespace Database\Seeders;

use App\Models\Profile\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['key' => 'first_note', 'name' => 'First Note', 'description' => 'Create your first note', 'icon' => 'note_add', 'category' => 'milestone', 'target_type' => 'notes_count', 'target_value' => 1],
            ['key' => 'note_collector', 'name' => 'Note Collector', 'description' => 'Create 10 notes', 'icon' => 'library_books', 'category' => 'milestone', 'target_type' => 'notes_count', 'target_value' => 10],
            ['key' => 'prolific_writer', 'name' => 'Prolific Writer', 'description' => 'Create 50 notes', 'icon' => 'auto_stories', 'category' => 'milestone', 'target_type' => 'notes_count', 'target_value' => 50],
            ['key' => 'first_task', 'name' => 'First Task', 'description' => 'Complete your first task', 'icon' => 'task_alt', 'category' => 'milestone', 'target_type' => 'tasks_completed', 'target_value' => 1],
            ['key' => 'task_master', 'name' => 'Task Master', 'description' => 'Complete 25 tasks', 'icon' => 'checklist', 'category' => 'milestone', 'target_type' => 'tasks_completed', 'target_value' => 25],
            ['key' => 'first_study', 'name' => 'First Study', 'description' => 'Complete your first study session', 'icon' => 'school', 'category' => 'milestone', 'target_type' => 'study_sessions', 'target_value' => 1],
            ['key' => 'study_streak_7', 'name' => 'Study Streak', 'description' => 'Study 7 days in a row', 'icon' => 'local_fire_department', 'category' => 'streak', 'target_type' => 'study_streak', 'target_value' => 7],
            ['key' => 'study_marathon', 'name' => 'Study Marathon', 'description' => 'Study for 10 hours total', 'icon' => 'timer', 'category' => 'milestone', 'target_type' => 'study_hours', 'target_value' => 10],
            ['key' => 'early_bird', 'name' => 'Early Bird', 'description' => 'Set your first countdown', 'icon' => 'alarm', 'category' => 'milestone', 'target_type' => 'countdowns_count', 'target_value' => 1],
            ['key' => 'planner', 'name' => 'Planner', 'description' => 'Create 5 plans', 'icon' => 'event_note', 'category' => 'milestone', 'target_type' => 'plans_count', 'target_value' => 5],
            ['key' => 'profile_complete', 'name' => 'Profile Complete', 'description' => 'Fill out your profile', 'icon' => 'person', 'category' => 'milestone', 'target_type' => 'profile_complete', 'target_value' => 1],
            ['key' => 'customizer', 'name' => 'Customizer', 'description' => 'Upload a custom cover image', 'icon' => 'palette', 'category' => 'milestone', 'target_type' => 'custom_cover', 'target_value' => 1],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['key' => $achievement['key']],
                $achievement,
            );
        }
    }
}
