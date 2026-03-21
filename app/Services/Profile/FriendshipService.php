<?php

namespace App\Services\Profile;

use App\Models\Profile\Friendship;
use App\Models\Profile\UserProfile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class FriendshipService
{
    public function listFriends(int $userId): array
    {
        $friendIds = Friendship::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('friend_id', $userId);
        })
            ->where('status', 'accepted')
            ->get()
            ->map(fn (Friendship $f) => $f->user_id === $userId ? $f->friend_id : $f->user_id)
            ->toArray();

        return UserProfile::whereIn('user_id', $friendIds)
            ->get()
            ->map(fn (UserProfile $p) => [
                'user_id' => $p->user_id,
                'username' => $p->username,
                'status' => $p->status,
                'avatar_url' => $p->avatar_url,
            ])
            ->toArray();
    }

    public function listPendingRequests(int $userId): array
    {
        $requests = Friendship::where('friend_id', $userId)
            ->where('status', 'pending')
            ->get();

        $senderIds = $requests->pluck('user_id')->toArray();
        $profiles = UserProfile::whereIn('user_id', $senderIds)
            ->get()
            ->keyBy('user_id');

        return $requests->map(function (Friendship $f) use ($profiles) {
            $profile = $profiles->get($f->user_id);
            return [
                'id' => $f->id,
                'user_id' => $f->user_id,
                'username' => $profile?->username,
                'avatar_url' => $profile?->avatar_url,
                'created_at' => $f->created_at,
            ];
        })->toArray();
    }

    public function sendRequest(int $userId, int $friendId): Friendship
    {
        if ($userId === $friendId) {
            throw new HttpException(422, 'Cannot send friend request to yourself');
        }

        // Check if any relationship exists in either direction
        $existing = Friendship::where(function ($q) use ($userId, $friendId) {
            $q->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function ($q) use ($userId, $friendId) {
            $q->where('user_id', $friendId)->where('friend_id', $userId);
        })->first();

        if ($existing) {
            throw new HttpException(422, 'Friend request already exists');
        }

        return Friendship::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => 'pending',
        ]);
    }

    public function acceptRequest(int $userId, int $requestId): Friendship
    {
        $friendship = Friendship::where('id', $requestId)
            ->where('friend_id', $userId)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'accepted']);

        return $friendship;
    }

    public function rejectRequest(int $userId, int $requestId): void
    {
        $friendship = Friendship::where('id', $requestId)
            ->where('friend_id', $userId)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->delete();
    }

    public function removeFriend(int $userId, int $friendUserId): void
    {
        $friendship = Friendship::where(function ($q) use ($userId, $friendUserId) {
            $q->where('user_id', $userId)->where('friend_id', $friendUserId);
        })->orWhere(function ($q) use ($userId, $friendUserId) {
            $q->where('user_id', $friendUserId)->where('friend_id', $userId);
        })
            ->where('status', 'accepted')
            ->firstOrFail();

        $friendship->delete();
    }
}
