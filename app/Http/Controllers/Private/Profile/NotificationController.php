<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Services\Profile\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $service
    ) {
    }

    public function registerToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|in:android,ios',
        ]);

        $this->service->registerToken(
            $request->user()->id,
            $request->input('token'),
            $request->input('platform'),
        );

        return ApiResponse::success(null, 'Token registered');
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $counts = $this->service->getUnreadCounts($request->user()->id);
        return ApiResponse::success($counts);
    }
}
