<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\AppRelease\AppReleaseResource;
use App\Services\AppReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppReleaseController extends Controller
{

    public function __construct(
        protected AppReleaseService $appReleaseService
    )
    {
    }

    public function latest(Request $request): JsonResponse
    {
        $platform = $request->query('platform', 'android');
        $channel = $request->query('channel', 'stable');
        return ApiResponse::success(new AppReleaseResource($this->appReleaseService->getLatest($platform, $channel)));
    }

    public function downloadLatest(Request $request): RedirectResponse
    {
        $platform = $request->query('platform', 'android');
        $channel = $request->query('channel', 'stable');
        return redirect()->away($this->appReleaseService->getDownloadUrl($platform, $channel));
    }
}
