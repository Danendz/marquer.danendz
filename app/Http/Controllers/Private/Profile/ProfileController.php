<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpsertProfileRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Profile\UserProfileResource;
use App\Services\Profile\UserProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected UserProfileService $service
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $profile = $this->service->get($request->user()->id);
        return ApiResponse::success(new UserProfileResource($profile));
    }

    public function update(UpsertProfileRequest $request): JsonResponse
    {
        $profile = $this->service->upsert($request->user()->id, $request->validated());
        return ApiResponse::success(new UserProfileResource($profile));
    }
}
