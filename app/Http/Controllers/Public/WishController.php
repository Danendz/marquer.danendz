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
     * Fetches wishes for the provided list of IDs and returns them as a JSON resource collection.
     *
     * @param GetWishesByIds $request Request containing validated `ids` (array of wish IDs).
     * @return JsonResponse JSON response with a collection of wishes wrapped in `WishResource`.
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
     * Fetches a random wish excluding any IDs provided in the request and returns it as a JSON success response.
     *
     * The request may include an `exclude_ids` array of wish IDs that should not be considered when selecting the random wish.
     *
     * @param GetRandomWishRequest $request Request containing optional `exclude_ids`.
     * @return JsonResponse JSON success response containing the selected wish serialized with WishResource.
     */
    public function random(GetRandomWishRequest $request): JsonResponse
    {
        $data = $request->validated();
        $excludeIds = $data['exclude_ids'] ?? [];
        $wish = $this->wishService->getRandomWish($excludeIds);

        return ApiResponse::success(new WishResource($wish));
    }
}