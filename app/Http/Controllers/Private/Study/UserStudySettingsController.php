<?php

namespace App\Http\Controllers\Private\Study;

use App\Http\Controllers\Controller;
use App\Http\Requests\Study\UpsertUserStudySettingsRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Study\UserStudySettingsResource;
use App\Services\Study\UserStudySettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserStudySettingsController extends Controller
{
    public function __construct(
        protected UserStudySettingsService $service
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $settings = $this->service->get($request->user()->id);
        return ApiResponse::success(new UserStudySettingsResource($settings));
    }

    public function upsert(UpsertUserStudySettingsRequest $request): JsonResponse
    {
        $settings = $this->service->upsert($request->user()->id, $request->validated());
        return ApiResponse::success(new UserStudySettingsResource($settings));
    }
}
