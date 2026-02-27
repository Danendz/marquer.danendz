<?php

namespace App\Services\Study;

use App\Models\Study\UserStudySettings;
use App\Services\RabbitPublisherService;
use Illuminate\Support\Facades\DB;

readonly class UserStudySettingsService
{
    public function __construct(
        private RabbitPublisherService $publisher
    ) {
    }

    public function get(int $userId): UserStudySettings
    {
        return UserStudySettings::firstOrNew(['user_id' => $userId], [
            'default_work_minutes' => 25,
            'default_short_break_minutes' => 5,
            'default_long_break_minutes' => 15,
            'default_cycles' => 4,
        ]);
    }

    public function upsert(int $userId, array $data): UserStudySettings
    {
        return DB::transaction(function () use ($userId, $data) {
            $settings = UserStudySettings::updateOrCreate(['user_id' => $userId], $data);

            DB::afterCommit(function () use ($settings) {
                $this->publisher->publishAnalytics('study.settings_updated', [
                    'event_name' => 'study_settings_updated',
                    'properties' => [
                        'work_minutes' => $settings->default_work_minutes,
                        'short_break_minutes' => $settings->default_short_break_minutes,
                        'long_break_minutes' => $settings->default_long_break_minutes,
                        'cycles' => $settings->default_cycles,
                    ],
                ]);
            });

            return $settings;
        });
    }
}
