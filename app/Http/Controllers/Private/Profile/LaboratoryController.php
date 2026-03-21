<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ToggleLabFeatureRequest;
use App\Http\Resources\ApiResponse;
use App\Services\Profile\LaboratoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function __construct(
        protected LaboratoryService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $features = $this->service->get($request->user()->id);
        return ApiResponse::success($features);
    }

    public function toggle(ToggleLabFeatureRequest $request): JsonResponse
    {
        $features = $this->service->toggle(
            $request->user()->id,
            $request->validated('feature'),
            $request->validated('enabled'),
        );

        return ApiResponse::success($features);
    }
}
