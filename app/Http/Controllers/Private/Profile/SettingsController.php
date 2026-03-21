<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpsertSettingsRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Profile\UserSettingsResource;
use App\Services\Profile\UserSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        protected UserSettingsService $service
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $settings = $this->service->get($request->user()->id);
        return ApiResponse::success(new UserSettingsResource($settings));
    }

    public function upsert(UpsertSettingsRequest $request): JsonResponse
    {
        $settings = $this->service->upsert($request->user()->id, $request->validated());
        return ApiResponse::success(new UserSettingsResource($settings));
    }
}
