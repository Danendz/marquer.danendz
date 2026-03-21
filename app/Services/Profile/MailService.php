<?php

namespace App\Services\Profile;

use App\Models\Profile\Friendship;
use App\Models\Profile\MailItem;
use App\Models\Profile\UserProfile;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class MailService
{
    public function send(int $senderId, int $recipientId, string $templateType, string $templateKey, string $message): MailItem
    {
        // Verify friendship
        $isFriend = Friendship::where(function ($q) use ($senderId, $recipientId) {
            $q->where('user_id', $senderId)->where('friend_id', $recipientId);
        })->orWhere(function ($q) use ($senderId, $recipientId) {
            $q->where('user_id', $recipientId)->where('friend_id', $senderId);
        })
            ->where('status', 'accepted')
            ->exists();

        if (!$isFriend) {
            throw new HttpException(403, 'You can only send mail to friends');
        }

        return MailItem::create([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'template_type' => $templateType,
            'template_key' => $templateKey,
            'message' => $message,
        ]);
    }

    public function listReceived(int $userId): array
    {
        $items = MailItem::where('recipient_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $senderIds = $items->pluck('sender_id')->unique()->toArray();
        $profiles = UserProfile::whereIn('user_id', $senderIds)->get()->keyBy('user_id');

        return $items->map(function (MailItem $item) use ($profiles) {
            $sender = $profiles->get($item->sender_id);
            return [
                'id' => $item->id,
                'sender_id' => $item->sender_id,
                'sender_username' => $sender?->username,
                'sender_avatar_url' => $sender?->avatar_url,
                'template_type' => $item->template_type,
                'template_key' => $item->template_key,
                'message' => $item->message,
                'read_at' => $item->read_at,
                'created_at' => $item->created_at,
            ];
        })->toArray();
    }

    public function listSent(int $userId): array
    {
        return MailItem::where('sender_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (MailItem $item) => [
                'id' => $item->id,
                'recipient_id' => $item->recipient_id,
                'template_type' => $item->template_type,
                'template_key' => $item->template_key,
                'message' => $item->message,
                'created_at' => $item->created_at,
            ])
            ->toArray();
    }

    public function markRead(int $userId, int $mailItemId): void
    {
        $item = MailItem::where('id', $mailItemId)
            ->where('recipient_id', $userId)
            ->firstOrFail();

        $item->update(['read_at' => now()]);
    }
}
