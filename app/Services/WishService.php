<?php

namespace App\Services;

use App\Models\Note;
use App\Models\Wish;
use Illuminate\Database\Eloquent\Collection;

class WishService
{
    public function getRandomWish(array $excludeIds): Wish
    {
        return Wish::query()
            ->whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->firstOrFail();
    }

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
