<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Services\Profile\AchievementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function __construct(
        protected AchievementService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $achievements = $this->service->listForUser($request->user()->id);
        return ApiResponse::success($achievements);
    }
}
