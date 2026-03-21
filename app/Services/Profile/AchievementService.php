<?php

namespace App\Services\Profile;

use App\Models\Profile\Achievement;
use App\Models\Profile\UserAchievement;

readonly class AchievementService
{
    public function listForUser(int $userId): array
    {
        $achievements = Achievement::all();
        $unlocked = UserAchievement::where('user_id', $userId)
            ->pluck('unlocked_at', 'achievement_id')
            ->toArray();

        return $achievements->map(function (Achievement $a) use ($unlocked) {
            return [
                'id' => $a->id,
                'key' => $a->key,
                'name' => $a->name,
                'description' => $a->description,
                'icon' => $a->icon,
                'category' => $a->category,
                'target_type' => $a->target_type,
                'target_value' => $a->target_value,
                'unlocked' => isset($unlocked[$a->id]),
                'unlocked_at' => $unlocked[$a->id] ?? null,
            ];
        })->toArray();
    }

    public function checkAndUnlock(int $userId, string $targetType, int $currentValue): void
    {
        $achievements = Achievement::where('target_type', $targetType)
            ->where('target_value', '<=', $currentValue)
            ->get();

        foreach ($achievements as $achievement) {
            UserAchievement::firstOrCreate([
                'user_id' => $userId,
                'achievement_id' => $achievement->id,
            ], [
                'unlocked_at' => now(),
            ]);
        }
    }
}
