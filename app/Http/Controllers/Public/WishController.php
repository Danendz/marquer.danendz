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
        $list_ids = $data['ids'] ?? [];
        $wishes = $this->wishService->get_wishes_by_ids($list_ids);

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
        $exclude_ids = $data['exclude_ids'] ?? [];
        $wish = $this->wishService->get_random_wish($exclude_ids);

        return ApiResponse::success(new WishResource($wish));
    }
}
