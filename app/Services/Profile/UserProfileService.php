<?php

namespace App\Services\Profile;

use App\Models\Profile\UserProfile;
use App\Services\AnalyticsPublisherService;
use Illuminate\Support\Facades\DB;

readonly class UserProfileService
{
    public function __construct(
        private AnalyticsPublisherService $publisher
    ) {
    }

    public function get(int $userId): UserProfile
    {
        return UserProfile::firstOrCreate(['user_id' => $userId], [
            'cover_type' => 'static',
        ]);
    }

    public function upsert(int $userId, array $data): UserProfile
    {
        return DB::transaction(function () use ($userId, $data) {
            $profile = UserProfile::updateOrCreate(['user_id' => $userId], $data);

            DB::afterCommit(function () use ($profile) {
                $this->publisher->publish('profile_updated', [
                    'username' => $profile->username,
                ]);
            });

            return $profile;
        });
    }
}
