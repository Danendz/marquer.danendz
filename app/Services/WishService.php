<?php

namespace App\Services;

use App\Models\Note;
use App\Models\Wish;
use Illuminate\Database\Eloquent\Collection;

class WishService
{
    /**
     * Selects a random Wish that is not in the provided list of IDs.
     *
     * @param int[] $excludeIds IDs of wishes to exclude from selection.
     * @return Wish The selected Wish.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching Wish exists.
     */
    public function getRandomWish(array $excludeIds): Wish
    {
        return Wish::query()
            ->whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->firstOrFail();
    }

    /**
     * Retrieve wishes matching the given IDs.
     *
     * @param int[] $ids Array of wish IDs to fetch.
     * @return \Illuminate\Database\Eloquent\Collection Collection of Wish models matching the provided IDs.
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