<?php

namespace App\Services\Profile;

use App\Models\Profile\UserSettings;
use App\Services\AnalyticsPublisherService;
use Illuminate\Support\Facades\DB;

readonly class UserSettingsService
{
    public function __construct(
        private AnalyticsPublisherService $publisher
    ) {
    }

    public function get(int $userId): UserSettings
    {
        return UserSettings::firstOrCreate(['user_id' => $userId], [
            'theme' => 'light',
            'language' => 'en',
            'notifications_enabled' => true,
            'notification_study_reminders' => true,
            'notification_task_reminders' => true,
            'lab_features' => [],
        ]);
    }

    public function upsert(int $userId, array $data): UserSettings
    {
        return DB::transaction(function () use ($userId, $data) {
            $settings = UserSettings::updateOrCreate(['user_id' => $userId], $data);

            DB::afterCommit(function () use ($settings) {
                $this->publisher->publish('settings_updated', [
                    'theme' => $settings->theme,
                    'language' => $settings->language,
                ]);
            });

            return $settings;
        });
    }
}
