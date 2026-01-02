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

    public function random(GetRandomWishRequest $request): JsonResponse
    {
        $data = $request->validated();
        $excludeIds = $data['exclude_ids'] ?? [];
        $wish = $this->wishService->getRandomWish($excludeIds);

        return ApiResponse::success(new WishResource($wish));
    }
}
