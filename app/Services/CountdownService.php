<?php

namespace App\Services;

use App\Models\Countdown;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class CountdownService
{
    private const BG_IMAGES = [
        'IMG_1157.webp', 'IMG_1158.webp', 'IMG_1159.webp', 'IMG_1160.webp',
        'IMG_1161.webp', 'IMG_1162.webp', 'IMG_1163.webp', 'IMG_1164.webp',
        'IMG_1165.webp', 'IMG_1166.webp', 'IMG_1167.webp', 'IMG_1168.webp',
        'IMG_1169.webp', 'IMG_1170.webp', 'IMG_1171.webp', 'IMG_1172.webp',
        'IMG_1173.webp', 'IMG_1174.webp', 'IMG_1175.webp',
    ];

    public function list(int $userId): Collection
    {
        return Countdown::where('user_id', $userId)
            ->orderByDesc('is_pinned')
            ->orderBy('target_date')
            ->get();
    }

    public function create(int $userId, array $data): Countdown
    {
        return Countdown::create([
            ...$data,
            'user_id' => $userId,
            'bg_image' => self::BG_IMAGES[array_rand(self::BG_IMAGES)],
            'is_pinned' => false,
        ]);
    }

    public function update(Countdown $countdown, array $data): Countdown
    {
        return DB::transaction(function () use ($countdown, $data) {
            if (isset($data['is_pinned']) && $data['is_pinned'] === true) {
                Countdown::where('user_id', $countdown->user_id)
                    ->where('id', '!=', $countdown->id)
                    ->where('is_pinned', true)
                    ->update(['is_pinned' => false]);
            }

            $countdown->update($data);
            return $countdown;
        });
    }

    public function delete(Countdown $countdown): void
    {
        $countdown->delete();
    }
}
