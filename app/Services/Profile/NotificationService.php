<?php

namespace App\Services\Profile;

use App\Models\Profile\DeviceToken;
use App\Models\Profile\Friendship;
use App\Models\Profile\MailItem;

readonly class NotificationService
{
    public function registerToken(int $userId, string $token, string $platform): DeviceToken
    {
        return DeviceToken::updateOrCreate(
            ['user_id' => $userId, 'token' => $token],
            ['platform' => $platform],
        );
    }

    public function getUnreadCounts(int $userId): array
    {
        $pendingRequests = Friendship::where('friend_id', $userId)
            ->where('status', 'pending')
            ->count();

        $unreadMail = MailItem::where('recipient_id', $userId)
            ->whereNull('read_at')
            ->count();

        return [
            'pending_requests' => $pendingRequests,
            'unread_mail' => $unreadMail,
            'total' => $pendingRequests + $unreadMail,
        ];
    }
}
