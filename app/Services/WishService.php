<?php

namespace App\Services;

use App\Models\Note;
use App\Models\Wish;
use Illuminate\Database\Eloquent\Collection;

class WishService
{
    /**
     * Selects a random Wish excluding the given IDs.
     *
     * @param int[] $excludeIds Array of Wish IDs to exclude from selection.
     * @return \App\Models\Wish The randomly selected Wish.
     */
    public function getRandomWish(array $excludeIds): Wish
    {
        return Wish::query()
            ->whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->firstOrFail();
    }

    /**
     * Retrieve wishes that match the provided IDs.
     *
     * @param int[] $ids Array of wish IDs to fetch.
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Wish[] Collection of Wish models matching the provided IDs.
     */
    public function getWishesByIds(array $ids): Collection
    {
        return Wish::query()
            ->whereIn('id', $ids)->get();
    }

    public function create(array $data): Wish
    {
        return Wish::create($data);
    }
}