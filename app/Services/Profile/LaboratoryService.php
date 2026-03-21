<?php

namespace App\Services\Profile;

use App\Models\Profile\UserSettings;

readonly class LaboratoryService
{
    public function get(int $userId): array
    {
        $settings = UserSettings::firstOrCreate(['user_id' => $userId], [
            'lab_features' => [],
        ]);

        return $settings->lab_features ?? [];
    }

    public function toggle(int $userId, string $feature, bool $enabled): array
    {
        $settings = UserSettings::firstOrCreate(['user_id' => $userId], [
            'lab_features' => [],
        ]);

        $features = $settings->lab_features ?? [];
        $features[$feature] = $enabled;

        $settings->update(['lab_features' => $features]);

        return $features;
    }
}
