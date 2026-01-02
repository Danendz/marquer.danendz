<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wishes\GetRandomWishRequest;
use App\Http\Requests\Wishes\GetWishesByIds;
use App\Http\Requests\Wishes\StoreWishRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Wishes\WishResource;
use App\Services\WishService;
use Illuminate\Http\JsonResponse;

class WishController extends Controller
{
    public function __construct(
        protected WishService $wishService
    )
    {
    }

    /**
     * Retrieve wishes matching the provided list of IDs and return them as a resource collection.
     *
     * @param \App\Http\Requests\Wishes\GetWishesByIds $request Request containing an `ids` array of wish IDs to fetch (defaults to an empty array).
     * @return \Illuminate\Http\JsonResponse JSON response containing a collection of `WishResource` objects for the matching wishes.
     */
    public function index(GetWishesByIds $request): JsonResponse
    {
        $data = $request->validated();
        $listIds = $data['ids'] ?? [];
        $wishes = $this->wishService->getWishesByIds($listIds);

        return ApiResponse::success(WishResource::collection($wishes));
    }

    public function store(StoreWishRequest $request): JsonResponse
    {
        $wish = $this->wishService->create($request->validated());

        return ApiResponse::success(new WishResource($wish));
    }

    /**
     * Selects a random wish excluding the IDs provided in the request and returns it as an API response.
     *
     * @param GetRandomWishRequest $request Request containing an optional `exclude_ids` array of wish IDs to exclude.
     * @return JsonResponse A successful ApiResponse containing a WishResource representation of the selected random wish.
     */
    public function random(GetRandomWishRequest $request): JsonResponse
    {
        $data = $request->validated();
        $excludeIds = $data['exclude_ids'] ?? [];
        $wish = $this->wishService->getRandomWish($excludeIds);

        return ApiResponse::success(new WishResource($wish));
    }
}