<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\GenerateUploadUrlRequest;
use App\Http\Resources\ApiResponse;
use App\Services\Profile\ProfileUploadService;
use Illuminate\Http\JsonResponse;

class ProfileUploadController extends Controller
{
    public function __construct(
        protected ProfileUploadService $service
    ) {
    }

    public function avatarUploadUrl(GenerateUploadUrlRequest $request): JsonResponse
    {
        $result = $this->service->generateUploadUrl(
            $request->user()->id,
            'avatar',
            $request->validated('content_type'),
        );

        return ApiResponse::success($result);
    }

    public function coverUploadUrl(GenerateUploadUrlRequest $request): JsonResponse
    {
        $result = $this->service->generateUploadUrl(
            $request->user()->id,
            'cover',
            $request->validated('content_type'),
        );

        return ApiResponse::success($result);
    }
}
