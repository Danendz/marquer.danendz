<?php

namespace App\Services;

use App\Models\Countdown;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class CountdownService
{
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
